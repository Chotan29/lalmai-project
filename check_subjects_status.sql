-- Check subjects count
SELECT COUNT(*) as total_subjects FROM subjects;

-- Check semester_subject mappings
SELECT COUNT(*) as total_mappings FROM semester_subject;

-- Check subjects per semester
SELECT s.id, s.semester, COUNT(ss.subject_id) as subject_count
FROM semesters s
LEFT JOIN semester_subject ss ON s.id = ss.semester_id
GROUP BY s.id, s.semester
ORDER BY s.id;

-- Check if subjects table is empty
SELECT COUNT(*) FROM subjects WHERE deleted_at IS NULL;
