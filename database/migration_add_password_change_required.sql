-- Migration to add password_change_required column to users table
-- Run this query to update your database

ALTER TABLE `users` ADD COLUMN `password_change_required` TINYINT(1) DEFAULT 0 AFTER `last_activity`;

-- Optional: Update existing users to require password change if needed
-- UPDATE `users` SET `password_change_required` = 1 WHERE `id_user` > 0;
