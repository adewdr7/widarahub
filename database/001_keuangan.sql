CREATE TABLE IF NOT EXISTS village_budgets (
  id INTEGER PRIMARY KEY AUTOINCREMENT, year INTEGER NOT NULL, kind TEXT NOT NULL DEFAULT 'belanja', sector TEXT DEFAULT '',
  title TEXT NOT NULL, planned INTEGER NOT NULL DEFAULT 0, realized INTEGER NOT NULL DEFAULT 0, note TEXT DEFAULT '',
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE IF NOT EXISTS village_goals (
  id INTEGER PRIMARY KEY AUTOINCREMENT, pillar TEXT NOT NULL DEFAULT 'ekonomi', title TEXT NOT NULL, description TEXT DEFAULT '',
  indicator TEXT DEFAULT '', baseline_value INTEGER DEFAULT 0, current_value INTEGER DEFAULT 0, target_value INTEGER DEFAULT 0,
  unit TEXT DEFAULT '', target_year INTEGER,
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE IF NOT EXISTS village_needs (
  id INTEGER PRIMARY KEY AUTOINCREMENT, category TEXT NOT NULL DEFAULT 'usaha_jasa', title TEXT NOT NULL, description TEXT DEFAULT '',
  status_need TEXT NOT NULL DEFAULT 'dibutuhkan', priority INTEGER NOT NULL DEFAULT 0, contact TEXT DEFAULT '',
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE INDEX IF NOT EXISTS idx_budgets_year ON village_budgets (year, kind);
