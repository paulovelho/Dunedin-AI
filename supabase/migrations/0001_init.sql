-- Dunedin AI initial schema
-- Tables: users, highlights, notes, files
-- Auth: extends Supabase auth.users via public.users
-- Security: RLS on all tables, scoped to auth.uid()

-- ============================================================================
-- users (mirrors auth.users; extends with app-specific columns)
-- ============================================================================
create table public.users (
  id uuid primary key references auth.users(id) on delete cascade,
  email text not null,
  last_login timestamptz,
  active boolean not null default true,
  status text,
  created_at timestamptz not null default now()
);

-- Auto-create a public.users row whenever a new auth.users row is created.
create or replace function public.handle_new_user()
returns trigger
language plpgsql
security definer set search_path = public
as $$
begin
  insert into public.users (id, email)
  values (new.id, new.email);
  return new;
end;
$$;

create trigger on_auth_user_created
  after insert on auth.users
  for each row execute function public.handle_new_user();

-- ============================================================================
-- highlights
-- ============================================================================
create table public.highlights (
  id uuid primary key default gen_random_uuid(),
  user_id uuid not null references public.users(id) on delete cascade,
  text text not null,
  origin text not null check (origin in ('kindle', 'web', 'twitter')),
  author text,
  date timestamptz not null default now(),
  hash text not null,
  created_at timestamptz not null default now(),
  unique (user_id, hash)
);

create index highlights_user_id_idx on public.highlights(user_id);
create index highlights_author_idx on public.highlights(author);

-- ============================================================================
-- notes
-- ============================================================================
create table public.notes (
  id uuid primary key default gen_random_uuid(),
  highlight_id uuid not null references public.highlights(id) on delete cascade,
  user_id uuid not null references public.users(id) on delete cascade,
  note text not null,
  date timestamptz not null default now(),
  created_at timestamptz not null default now()
);

create index notes_highlight_id_idx on public.notes(highlight_id);
create index notes_user_id_idx on public.notes(user_id);

-- ============================================================================
-- files
-- ============================================================================
create table public.files (
  id uuid primary key default gen_random_uuid(),
  user_id uuid not null references public.users(id) on delete cascade,
  filename text not null,
  type text not null check (type in ('kindle3')),
  imported_date timestamptz,
  status text not null default 'pending',
  created_at timestamptz not null default now()
);

create index files_user_id_idx on public.files(user_id);

-- ============================================================================
-- Row Level Security
-- ============================================================================
alter table public.users enable row level security;
alter table public.highlights enable row level security;
alter table public.notes enable row level security;
alter table public.files enable row level security;

create policy "users select own"
  on public.users for select
  using (id = auth.uid());

create policy "users update own"
  on public.users for update
  using (id = auth.uid());

create policy "highlights all own"
  on public.highlights for all
  using (user_id = auth.uid())
  with check (user_id = auth.uid());

create policy "notes all own"
  on public.notes for all
  using (user_id = auth.uid())
  with check (user_id = auth.uid());

create policy "files all own"
  on public.files for all
  using (user_id = auth.uid())
  with check (user_id = auth.uid());
