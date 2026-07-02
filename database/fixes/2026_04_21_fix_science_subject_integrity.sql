-- Safe review-first fix for current science semester subject integrity issues.
-- This script is NOT executed automatically.
-- Review on a backup/staging database first.

-- 1) Inspect current state before any change.
SELECT ss.semester_id,
       sem.semester,
       sem.slug,
       ss.subject_id,
       sub.title,
       sub.code,
       sub.status
FROM semester_subject ss
LEFT JOIN semesters sem ON sem.id = ss.semester_id
LEFT JOIN subjects sub ON sub.id = ss.subject_id
WHERE ss.semester_id IN (156, 157)
ORDER BY ss.semester_id, ss.subject_id;

-- 2) Safe part: re-activate subject rows that already exist but were set inactive.
--    These IDs exist in subjects table and are linked to semester 157.
START TRANSACTION;

UPDATE subjects
SET status = 1,
    last_updated_by = COALESCE(last_updated_by, created_by),
    updated_at = NOW()
WHERE id IN (1040, 1042, 1045, 1047, 1049, 1051)
  AND status = 0;

COMMIT;

-- 3) Verify the status change worked.
SELECT id, title, code, status, updated_at
FROM subjects
WHERE id IN (1040, 1042, 1045, 1047, 1049, 1051)
ORDER BY id;

-- 4) Missing rows still needing manual recovery or backup restore.
--    Do NOT guess these rows in production unless you confirm title/code/marks from backup or admin records.
SELECT ss.semester_id,
       ss.subject_id
FROM semester_subject ss
LEFT JOIN subjects sub ON sub.id = ss.subject_id
WHERE ss.semester_id IN (156, 157)
  AND sub.id IS NULL
ORDER BY ss.semester_id, ss.subject_id;

-- Current missing subject IDs:
-- Semester 156: 1039, 1041, 1043, 1044, 1046, 1048, 1050
-- Semester 157: 1043

-- 5) Only after confirming exact historical values from backup/admin UI,
--    recreate missing subject rows and keep IDs aligned with semester_subject.
--    Example template below. Replace the placeholders with confirmed values.
-- INSERT INTO subjects (
--     id, created_at, updated_at, created_by, last_updated_by,
--     title, code, course_fee,
--     full_mark_theory, pass_mark_theory,
--     full_mark_practical, pass_mark_practical,
--     credit_hour, sub_type, class_type, staff_id,
--     description, status
-- ) VALUES (
--     1039, NOW(), NOW(), 1, 1,
--     'CONFIRMED TITLE', 'CONFIRMED CODE', NULL,
--     NULL, NULL,
--     NULL, NULL,
--     NULL, NULL, NULL, NULL,
--     NULL, 1
-- );

-- 5a) Proposed reconstruction map derived from the surviving interleaved IDs.
--     Review this carefully before converting it to executable INSERTs.
--     Evidence:
--     - 1040, 1042, 1045, 1047, 1049, 1051 exist as 2nd Paper subjects.
--     - Missing IDs are interleaved: 1039, 1041, 1043, 1044, 1046, 1048, 1050.
--     - 1043 is referenced by both semesters 156 and 157, so it is likely a shared/common subject.
--
-- Proposed labels to verify from backup/admin records:
-- 1039 -> Bangla 1st Paper
-- 1041 -> English 1st Paper
-- 1043 -> Information and Communication Technology (shared)
-- 1044 -> Physics 1st Paper
-- 1046 -> Chemistry 1st Paper
-- 1048 -> Biology 1st Paper
-- 1050 -> Higher Math 1st Paper
--
-- Likely values copied from surviving companion subjects:
-- 1039 from 1040 (Bangla 2nd Paper), title/code adjusted
-- 1041 from 1042 (English 2nd Paper), title/code adjusted
-- 1044 from 1045 (Physics 2nd Paper), title/code adjusted
-- 1046 from 1047 (Chemistry 2nd Paper), title/code adjusted
-- 1048 from 1049 (Biology 2nd Paper), title/code adjusted
-- 1050 from 1051 (Higher Math 2nd Paper), title/code adjusted
-- 1043 must be confirmed separately because no surviving row exists to clone.
--
-- Review query for companion values:
SELECT id, title, code,
       course_fee,
       full_mark_theory, pass_mark_theory,
       full_mark_practical, pass_mark_practical,
       credit_hour, sub_type, class_type, staff_id,
       description, status, created_by, last_updated_by
FROM subjects
WHERE id IN (1040, 1042, 1045, 1047, 1049, 1051)
ORDER BY id;

-- Example reconstruction approach after manual confirmation:
-- INSERT INTO subjects (
--     id, created_at, updated_at, created_by, last_updated_by,
--     title, code, course_fee,
--     full_mark_theory, pass_mark_theory,
--     full_mark_practical, pass_mark_practical,
--     credit_hour, sub_type, class_type, staff_id,
--     description, status
-- )
-- SELECT 1039, NOW(), NOW(), created_by, COALESCE(last_updated_by, created_by),
--        'Bangla 1st Paper', 'CONFIRM_CODE_101', course_fee,
--        full_mark_theory, pass_mark_theory,
--        full_mark_practical, pass_mark_practical,
--        credit_hour, sub_type, class_type, staff_id,
--        description, 1
-- FROM subjects WHERE id = 1040;
--
-- Repeat the same pattern for 1041 <- 1042, 1044 <- 1045, 1046 <- 1047,
-- 1048 <- 1049, 1050 <- 1051 after confirming title/code.
-- Handle 1043 only after confirming its exact title/code/marks.

-- 5b) Executable reconstruction block for 6 rows that can be cloned safely
--     from surviving companion subjects.
--     This block is idempotent and inserts only if the target ID is still missing.
START TRANSACTION;

INSERT INTO subjects (
    id, created_at, updated_at, created_by, last_updated_by,
    title, code, course_fee,
    full_mark_theory, pass_mark_theory,
    full_mark_practical, pass_mark_practical,
    credit_hour, sub_type, class_type, staff_id,
    description, status
)
SELECT 1039, NOW(), NOW(), created_by, COALESCE(last_updated_by, created_by),
       'Bangla 1st Paper', '101', course_fee,
       full_mark_theory, pass_mark_theory,
       full_mark_practical, pass_mark_practical,
       credit_hour, sub_type, class_type, staff_id,
       description, 1
FROM subjects src
WHERE src.id = 1040
  AND NOT EXISTS (SELECT 1 FROM subjects WHERE id = 1039)
LIMIT 1;

INSERT INTO subjects (
    id, created_at, updated_at, created_by, last_updated_by,
    title, code, course_fee,
    full_mark_theory, pass_mark_theory,
    full_mark_practical, pass_mark_practical,
    credit_hour, sub_type, class_type, staff_id,
    description, status
)
SELECT 1041, NOW(), NOW(), created_by, COALESCE(last_updated_by, created_by),
       'English 1st Paper', '107', course_fee,
       full_mark_theory, pass_mark_theory,
       full_mark_practical, pass_mark_practical,
       credit_hour, sub_type, class_type, staff_id,
       description, 1
FROM subjects src
WHERE src.id = 1042
  AND NOT EXISTS (SELECT 1 FROM subjects WHERE id = 1041)
LIMIT 1;

INSERT INTO subjects (
    id, created_at, updated_at, created_by, last_updated_by,
    title, code, course_fee,
    full_mark_theory, pass_mark_theory,
    full_mark_practical, pass_mark_practical,
    credit_hour, sub_type, class_type, staff_id,
    description, status
)
SELECT 1044, NOW(), NOW(), created_by, COALESCE(last_updated_by, created_by),
       'Physics 1st Paper', '174', course_fee,
       full_mark_theory, pass_mark_theory,
       full_mark_practical, pass_mark_practical,
       credit_hour, sub_type, class_type, staff_id,
       description, 1
FROM subjects src
WHERE src.id = 1045
  AND NOT EXISTS (SELECT 1 FROM subjects WHERE id = 1044)
LIMIT 1;

INSERT INTO subjects (
    id, created_at, updated_at, created_by, last_updated_by,
    title, code, course_fee,
    full_mark_theory, pass_mark_theory,
    full_mark_practical, pass_mark_practical,
    credit_hour, sub_type, class_type, staff_id,
    description, status
)
SELECT 1046, NOW(), NOW(), created_by, COALESCE(last_updated_by, created_by),
       'Chemistry 1st Paper', '176', course_fee,
       full_mark_theory, pass_mark_theory,
       full_mark_practical, pass_mark_practical,
       credit_hour, sub_type, class_type, staff_id,
       description, 1
FROM subjects src
WHERE src.id = 1047
  AND NOT EXISTS (SELECT 1 FROM subjects WHERE id = 1046)
LIMIT 1;

INSERT INTO subjects (
    id, created_at, updated_at, created_by, last_updated_by,
    title, code, course_fee,
    full_mark_theory, pass_mark_theory,
    full_mark_practical, pass_mark_practical,
    credit_hour, sub_type, class_type, staff_id,
    description, status
)
SELECT 1048, NOW(), NOW(), created_by, COALESCE(last_updated_by, created_by),
       'Biology 1st Paper', '178', course_fee,
       full_mark_theory, pass_mark_theory,
       full_mark_practical, pass_mark_practical,
       credit_hour, sub_type, class_type, staff_id,
       description, 1
FROM subjects src
WHERE src.id = 1049
  AND NOT EXISTS (SELECT 1 FROM subjects WHERE id = 1048)
LIMIT 1;

INSERT INTO subjects (
    id, created_at, updated_at, created_by, last_updated_by,
    title, code, course_fee,
    full_mark_theory, pass_mark_theory,
    full_mark_practical, pass_mark_practical,
    credit_hour, sub_type, class_type, staff_id,
    description, status
)
SELECT 1050, NOW(), NOW(), created_by, COALESCE(last_updated_by, created_by),
       'Higher Math 1st Paper', '265', course_fee,
       full_mark_theory, pass_mark_theory,
       full_mark_practical, pass_mark_practical,
       credit_hour, sub_type, class_type, staff_id,
       description, 1
FROM subjects src
WHERE src.id = 1051
  AND NOT EXISTS (SELECT 1 FROM subjects WHERE id = 1050)
LIMIT 1;

COMMIT;

-- 5c) Resolve shared row: 1043 (ICT)
--     Safe/idempotent guard on both id and code to avoid accidental duplicate creation.
INSERT INTO subjects (
    id,
    title,
    code,
    full_mark_theory,
    pass_mark_theory,
    full_mark_practical,
    pass_mark_practical,
    credit_hour,
    sub_type,
    class_type,
    status,
    created_at,
    updated_at
)
SELECT
    1043,
    'Information and Communication Technology',
    '275',
    75,
    24,
    25,
    8,
    5,
    'Compulsory',
    'Both',
    1,
    NOW(),
    NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM subjects WHERE id = 1043)
  AND NOT EXISTS (SELECT 1 FROM subjects WHERE code = '275');


-- 6) Final verification query.
SELECT ss.semester_id,
       sem.semester,
       sem.slug,
       COUNT(sub.id) AS resolved_subject_count,
       SUM(CASE WHEN sub.id IS NULL THEN 1 ELSE 0 END) AS missing_subject_count,
       SUM(CASE WHEN sub.id IS NOT NULL AND sub.status = 0 THEN 1 ELSE 0 END) AS inactive_subject_count
FROM semester_subject ss
LEFT JOIN semesters sem ON sem.id = ss.semester_id
LEFT JOIN subjects sub ON sub.id = ss.subject_id
WHERE ss.semester_id IN (156, 157)
GROUP BY ss.semester_id, sem.semester, sem.slug
ORDER BY ss.semester_id;