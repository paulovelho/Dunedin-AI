-- Dunedin AI initial schema (MariaDB)
-- Tables: users, files, imports, highlights, notes, shared_links, shared_link_highlights
-- Auth: Firebase-managed; users.firebase_uid is the link to Firebase Authentication

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  firebase_uid  VARCHAR(128)    NOT NULL,
  email         VARCHAR(255)    NOT NULL,
  display_name  VARCHAR(255)    NULL,
  photo_url     TEXT            NULL,
  last_login    DATETIME        NULL,
  active        TINYINT(1)      NOT NULL DEFAULT 1,
  status        VARCHAR(32)     NOT NULL DEFAULT 'active',
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME        NULL,
  PRIMARY KEY (id),
  UNIQUE KEY users_firebase_uid_uk (firebase_uid),
  KEY users_email_idx (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS files (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id        BIGINT UNSIGNED NOT NULL,
  filename       VARCHAR(512)    NOT NULL,
  type           VARCHAR(32)     NOT NULL,
  status         VARCHAR(32)     NOT NULL DEFAULT 'active',
  size           BIGINT          NOT NULL DEFAULT 0,
  created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME        NULL,
  PRIMARY KEY (id),
  KEY files_user_idx (user_id),
  CONSTRAINT files_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT files_type_ck CHECK (type IN ('kindle3'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS imports (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  file_id          BIGINT UNSIGNED NOT NULL,
  status           VARCHAR(32)     NOT NULL DEFAULT 'pending',
  highlight_count  INT             NOT NULL DEFAULT 0,
  execution_time   INT             NOT NULL DEFAULT 0,
  info             TEXT            NULL,
  imported_date    DATETIME        NULL,
  created_at       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY imports_file_idx (file_id),
  CONSTRAINT imports_file_fk FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS highlights (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     BIGINT UNSIGNED NOT NULL,
  import_id   BIGINT UNSIGNED NULL,
  text        TEXT            NOT NULL,
  origin      VARCHAR(32)     NOT NULL,
  author      VARCHAR(512)    NULL,
  title       VARCHAR(512)    NULL,
  date        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  hash        CHAR(64)        NOT NULL,
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME        NULL,
  PRIMARY KEY (id),
  UNIQUE KEY highlights_user_hash_uk (user_id, hash),
  KEY highlights_user_idx (user_id),
  KEY highlights_import_idx (import_id),
  KEY highlights_author_idx (author),
  KEY highlights_title_idx (title),
  CONSTRAINT highlights_user_fk   FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
  CONSTRAINT highlights_import_fk FOREIGN KEY (import_id) REFERENCES imports(id) ON DELETE SET NULL,
  CONSTRAINT highlights_origin_ck CHECK (origin IN ('kindle','web','twitter'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notes (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  highlight_id  BIGINT UNSIGNED NOT NULL,
  user_id       BIGINT UNSIGNED NOT NULL,
  note          TEXT            NOT NULL,
  date          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME        NULL,
  PRIMARY KEY (id),
  KEY notes_highlight_idx (highlight_id),
  KEY notes_user_idx (user_id),
  CONSTRAINT notes_highlight_fk FOREIGN KEY (highlight_id) REFERENCES highlights(id) ON DELETE CASCADE,
  CONSTRAINT notes_user_fk      FOREIGN KEY (user_id)      REFERENCES users(id)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shared_links (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id       BIGINT UNSIGNED NOT NULL,
  uuid          CHAR(36)        NOT NULL,
  active        TINYINT(1)      NOT NULL DEFAULT 1,
  description   VARCHAR(120)    NOT NULL,
  visits        BIGINT UNSIGNED NOT NULL DEFAULT 0,
  expire        DATETIME        NULL,
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME        NULL,
  PRIMARY KEY (id),
  UNIQUE KEY shared_links_uuid_uk (uuid),
  KEY shared_links_user_idx (user_id),
  CONSTRAINT shared_links_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shared_link_highlights (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  shared_link_id  BIGINT UNSIGNED NOT NULL,
  highlight_id    BIGINT UNSIGNED NOT NULL,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME        NULL,
  PRIMARY KEY (id),
  UNIQUE KEY shared_link_highlights_link_highlight_uk (shared_link_id, highlight_id),
  KEY shared_link_highlights_link_idx (shared_link_id),
  KEY shared_link_highlights_highlight_idx (highlight_id),
  CONSTRAINT shared_link_highlights_link_fk      FOREIGN KEY (shared_link_id) REFERENCES shared_links(id) ON DELETE CASCADE,
  CONSTRAINT shared_link_highlights_highlight_fk FOREIGN KEY (highlight_id)  REFERENCES highlights(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
