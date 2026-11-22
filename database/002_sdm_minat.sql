BEGIN;
ALTER TABLE potentials ADD COLUMN products TEXT DEFAULT '';
ALTER TABLE potentials ADD COLUMN price_range TEXT DEFAULT '';
ALTER TABLE potentials ADD COLUMN whatsapp TEXT DEFAULT '';
ALTER TABLE potentials ADD COLUMN consent_public INTEGER NOT NULL DEFAULT 0;
CREATE TABLE IF NOT EXISTS interests (
  id INTEGER PRIMARY KEY AUTOINCREMENT, ref_type TEXT NOT NULL, ref_id INTEGER NOT NULL, ref_title TEXT DEFAULT '',
  name TEXT NOT NULL, contact TEXT NOT NULL, note TEXT DEFAULT '', consent INTEGER NOT NULL DEFAULT 0,
  status TEXT NOT NULL DEFAULT 'baru', created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE INDEX IF NOT EXISTS idx_interests_ref ON interests (ref_type, ref_id);
CREATE TABLE IF NOT EXISTS trainings (
  id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, description TEXT DEFAULT '', goal_id INTEGER,
  starts_at TEXT, location TEXT DEFAULT '', quota INTEGER NOT NULL DEFAULT 0, is_open INTEGER NOT NULL DEFAULT 1,
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
COMMIT;
