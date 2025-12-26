-- Add Google ID column to nguoi_dung table for Google OAuth integration
-- Execute this SQL to update your database schema

-- Add google_id column (allows NULL for existing users)
ALTER TABLE nguoi_dung ADD COLUMN google_id VARCHAR(255) DEFAULT NULL;

-- Add unique index on google_id to prevent duplicate Google accounts
ALTER TABLE nguoi_dung ADD UNIQUE INDEX idx_google_id (google_id);

-- Note: Existing users can link their Google account by logging in with Google using the same email
