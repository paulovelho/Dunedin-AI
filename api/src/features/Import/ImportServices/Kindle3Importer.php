<?php
namespace Dunedin\Import\ImportServices;

use Dunedin\File\File;
use Dunedin\Highlight\Highlight;
use Dunedin\Highlight\HighlightControl;
use Dunedin\Import\Import;

class Kindle3Importer {

	private const UPLOAD_DIR = __DIR__ . '/../../../../storage/uploads';

	public static function Process(File $file): Import {
		$file->status = 'processing';
		$file->Save();

		$import          = new Import();
		$import->file_id = $file->id;
		$import->status  = 'pending';
		$import->Save();

		$startTime  = microtime(true);
		$filePath   = self::UPLOAD_DIR . '/' . $file->filename;
		$entries    = self::Parse($filePath);

		$imported   = 0;
		$skipped    = 0;
		$duplicates = [];

		foreach ($entries as $entry) {
			$hash     = hash('sha256', $file->user_id . $entry['text']);
			$existing = HighlightControl::GetRowWhere(['user_id' => $file->user_id, 'hash' => $hash]);

			if ($existing) {
				$duplicates[] = $existing->id;
				$skipped++;
				continue;
			}

			$highlight            = new Highlight();
			$highlight->user_id   = $file->user_id;
			$highlight->import_id = $import->id;
			$highlight->text      = $entry['text'];
			$highlight->origin    = 'kindle';
			$highlight->title     = $entry['title'];
			$highlight->author    = $entry['author'];
			$highlight->date      = $entry['date'];
			$highlight->hash      = $hash;
			$highlight->Save();

			$imported++;
		}

		$executionTime = (int)round((microtime(true) - $startTime) * 1000);
		$info          = json_encode(['skipped' => $skipped, 'duplicates' => $duplicates]);

		$import->status          = 'done';
		$import->highlight_count = $imported;
		$import->execution_time  = $executionTime;
		$import->imported_date   = date('Y-m-d H:i:s');
		$import->info            = $info;
		$import->Save();

		$file->status = 'imported';
		$file->Save();

		return $import;
	}

	public static function Parse(string $filePath): array {
		$contents = file_get_contents($filePath);
		// Strip UTF-8 BOM if present
		$contents = ltrim($contents, "\xEF\xBB\xBF");
		$blocks   = explode("==========", $contents);
		$entries  = [];

		foreach ($blocks as $block) {
			$block = trim($block);
			if (empty($block)) continue;

			$lines = explode("\n", $block);
			$lines = array_map('trim', $lines);
			$lines = array_values(array_filter($lines, fn($l) => $l !== ''));

			// Need at least a title line, a metadata line, and text
			if (count($lines) < 3) continue;

			// Line 0: "Title (Author)"
			[$title, $author] = self::parseTitleLine($lines[0]);

			// Line 1: "- Highlight ... | Added on Weekday, Month DD, YYYY, HH:MM AM/PM"
			$date = self::parseDate($lines[1]);

			// Lines 2+: highlight text
			$text = implode(" ", array_slice($lines, 2));
			$text = trim($text);

			if (empty($text)) continue;

			$entries[] = [
				'title'  => $title,
				'author' => $author,
				'text'   => $text,
				'date'   => $date,
			];
		}

		return $entries;
	}

	private static function parseTitleLine(string $line): array {
		// Extract the last (…) as author; everything before it is the title
		if (preg_match('/^(.*)\(([^)]+)\)\s*$/', $line, $m)) {
			return [trim($m[1]), trim($m[2])];
		}
		return [trim($line), ''];
	}

	private static function parseDate(string $line): string {
		// "Added on Saturday, December 31, 2022, 03:55 PM"
		if (preg_match('/Added on \w+, (\w+ \d+, \d+, \d+:\d+ [AP]M)/', $line, $m)) {
			$ts = strtotime($m[1]);
			if ($ts !== false) {
				return date('Y-m-d H:i:s', $ts);
			}
		}
		return date('Y-m-d H:i:s');
	}
}
