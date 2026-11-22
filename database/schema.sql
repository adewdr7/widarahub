CREATE TABLE roles (
  id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT NOT NULL UNIQUE, name TEXT NOT NULL, description TEXT DEFAULT ''
);
CREATE TABLE permissions (id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT NOT NULL UNIQUE);
CREATE TABLE role_permissions (
  role_id INTEGER NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
  permission_id INTEGER NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
  PRIMARY KEY (role_id, permission_id)
);
CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  role_id INTEGER NOT NULL REFERENCES roles(id),
  name TEXT NOT NULL, username TEXT NOT NULL UNIQUE, email TEXT DEFAULT '',
  password_hash TEXT NOT NULL, is_active INTEGER NOT NULL DEFAULT 1,
  last_login_at TEXT, created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE login_attempts (id INTEGER PRIMARY KEY AUTOINCREMENT, ip TEXT, username TEXT, attempted_at TEXT);

CREATE TABLE villages (
  id INTEGER PRIMARY KEY, name TEXT, district TEXT, regency TEXT, province TEXT, motto TEXT,
  address TEXT DEFAULT '', phone TEXT DEFAULT '', email TEXT DEFAULT '', whatsapp TEXT DEFAULT '',
  office_hours TEXT DEFAULT '', facebook TEXT DEFAULT '', instagram TEXT DEFAULT '', youtube TEXT DEFAULT '',
  is_verified INTEGER NOT NULL DEFAULT 0, updated_at TEXT
);
CREATE TABLE site_settings (setting_key TEXT PRIMARY KEY, setting_value TEXT);
CREATE TABLE village_statistics (
  id INTEGER PRIMARY KEY AUTOINCREMENT, label TEXT NOT NULL, value TEXT NOT NULL DEFAULT '', unit TEXT DEFAULT '',
  sort_order INTEGER DEFAULT 0, is_verified INTEGER NOT NULL DEFAULT 0, created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT, type TEXT NOT NULL, name TEXT NOT NULL, slug TEXT NOT NULL,
  created_at TEXT, updated_at TEXT, deleted_at TEXT, UNIQUE (type, slug)
);

CREATE TABLE pages (
  id INTEGER PRIMARY KEY AUTOINCREMENT, section TEXT NOT NULL DEFAULT 'profil', title TEXT NOT NULL, slug TEXT NOT NULL UNIQUE,
  body TEXT DEFAULT '', meta_description TEXT DEFAULT '', thumbnail TEXT DEFAULT '', sort_order INTEGER DEFAULT 0,
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE posts (
  id INTEGER PRIMARY KEY AUTOINCREMENT, category_id INTEGER, title TEXT NOT NULL, slug TEXT NOT NULL UNIQUE,
  excerpt TEXT DEFAULT '', body TEXT DEFAULT '', thumbnail TEXT DEFAULT '',
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE announcements (
  id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, slug TEXT NOT NULL UNIQUE, body TEXT DEFAULT '',
  priority INTEGER NOT NULL DEFAULT 0, expires_at TEXT,
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE events (
  id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, description TEXT DEFAULT '', location TEXT DEFAULT '',
  starts_at TEXT NOT NULL, ends_at TEXT,
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE potentials (
  id INTEGER PRIMARY KEY AUTOINCREMENT, category_id INTEGER, title TEXT NOT NULL, slug TEXT NOT NULL UNIQUE,
  description TEXT DEFAULT '', location TEXT DEFAULT '', hours TEXT DEFAULT '', contact TEXT DEFAULT '',
  manager TEXT DEFAULT '', thumbnail TEXT DEFAULT '',
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE government_members (
  id INTEGER PRIMARY KEY AUTOINCREMENT, unit TEXT NOT NULL DEFAULT 'pemerintah_desa', name TEXT NOT NULL,
  position TEXT DEFAULT '', description TEXT DEFAULT '', photo TEXT DEFAULT '', sort_order INTEGER DEFAULT 0,
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE public_documents (
  id INTEGER PRIMARY KEY AUTOINCREMENT, category_id INTEGER, title TEXT NOT NULL, description TEXT DEFAULT '',
  file_path TEXT DEFAULT '', year INTEGER,
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE services (
  id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, slug TEXT NOT NULL UNIQUE, summary TEXT DEFAULT '',
  requirements TEXT DEFAULT '', procedure TEXT DEFAULT '', form_file TEXT DEFAULT '', contact TEXT DEFAULT '',
  sort_order INTEGER DEFAULT 0,
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE galleries (
  id INTEGER PRIMARY KEY AUTOINCREMENT, category_id INTEGER, title TEXT NOT NULL, description TEXT DEFAULT '',
  media_type TEXT NOT NULL DEFAULT 'foto', image TEXT DEFAULT '', video_url TEXT DEFAULT '', album TEXT DEFAULT '',
  status TEXT NOT NULL DEFAULT 'draft', review_note TEXT DEFAULT '', published_at TEXT, created_by INTEGER,
  created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE media_files (
  id INTEGER PRIMARY KEY AUTOINCREMENT, original_name TEXT, path TEXT NOT NULL, mime TEXT, size INTEGER DEFAULT 0,
  folder TEXT DEFAULT 'umum', uploaded_by INTEGER, created_at TEXT
);
CREATE TABLE complaints (
  id INTEGER PRIMARY KEY AUTOINCREMENT, ticket TEXT NOT NULL UNIQUE, type TEXT NOT NULL DEFAULT 'pengaduan',
  name TEXT DEFAULT '', contact TEXT DEFAULT '', subject TEXT NOT NULL, message TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'baru', response TEXT DEFAULT '', created_at TEXT, updated_at TEXT, deleted_at TEXT
);
CREATE TABLE audit_logs (
  id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, username TEXT, action TEXT, table_name TEXT,
  record_id INTEGER, detail TEXT DEFAULT '', ip TEXT, created_at TEXT
);
CREATE TABLE revisions (
  id INTEGER PRIMARY KEY AUTOINCREMENT, table_name TEXT NOT NULL, record_id INTEGER NOT NULL,
  snapshot TEXT NOT NULL, user_id INTEGER, created_at TEXT
);
CREATE INDEX idx_posts_pub ON posts (status, published_at);
CREATE INDEX idx_announcements_pub ON announcements (status, published_at);
CREATE INDEX idx_events_start ON events (starts_at);
CREATE INDEX idx_potentials_cat ON potentials (category_id);
CREATE INDEX idx_audit_created ON audit_logs (created_at);
CREATE INDEX idx_revisions_rec ON revisions (table_name, record_id);
