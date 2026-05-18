
/* Stats */
$stats = [
    'total'    => (int) fetchValue($db, "SELECT COUNT(*) FROM tbl_exercises"),
    'home'     => (int) fetchValue($db, "SELECT COUNT(*) FROM tbl_exercises WHERE LOWER(location) = 'home'"),
    'advanced' => (int) fetchValue($db, "SELECT COUNT(*) FROM tbl_exercises WHERE LOWER(difficulty) = 'advanced'"),
    'muscles'  => (int) fetchValue($db, "SELECT COUNT(DISTINCT muscle) FROM tbl_exercises WHERE muscle IS NOT NULL AND muscle <> ''"),
];

/* Chart data */
$muscleRows     = fetchAllRows($db, "SELECT COALESCE(NULLIF(muscle,''),'Unassigned') AS label, COUNT(*) AS total FROM tbl_exercises GROUP BY label ORDER BY total DESC");
$difficultyRows = fetchAllRows($db, "SELECT COALESCE(NULLIF(difficulty,''),'Unassigned') AS label, COUNT(*) AS total FROM tbl_exercises GROUP BY label ORDER BY total DESC");