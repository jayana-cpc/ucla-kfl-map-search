-- Development seed data for quick smoke tests
-- Safe to run multiple times: uses INSERT ... ON DUPLICATE/IGNORE

INSERT INTO quarter (quarter_short_name, is_current_quarter)
VALUES ('SP25', 1)
ON DUPLICATE KEY UPDATE is_current_quarter = VALUES(is_current_quarter);

INSERT INTO collector (collector_id, collector_last_name, collector_first_name, collector_sid, collector_email, collector_status, collector_language)
VALUES (1, 'Admin', 'Dev', 'devadmin', 'dev@example.com', 2, 'english')
ON DUPLICATE KEY UPDATE collector_status = VALUES(collector_status);

INSERT INTO consultant (consultant_id, collector_id, consultant_last_name, consultant_first_name, consultant_gender, consultant_occupation, consultant_language, consultant_quarter_created)
VALUES (1, 1, 'Kim', 'Sample', 'F', 'Student', 'english', NULL)
ON DUPLICATE KEY UPDATE collector_id = VALUES(collector_id);

INSERT INTO context (context_id, collector_id, context_city, context_state, context_country, context_place, context_time, context_date, context_weather, context_language, context_media, context_event_type, context_event_name, context_otherpresent_num, context_description, context_consultants, context_spatial_point, context_quarter_created)
VALUES (
    1, 1, 'Los Angeles', 'CA', 'USA', 'public', '12:00:00', '2020-01-01',
    'sunny', 'english', 'audio', 'Storytelling', 'Sample Story',
    2, 'Sample context for dev demo', '1',
    ST_GeomFromText('POINT(-118.289 34.0219)'), NULL
)
ON DUPLICATE KEY UPDATE context_description = VALUES(context_description);

INSERT INTO data (data_id, collector_id, consultant_id, context_id, data_project_title, data_type, data_description, data_quarter_created)
VALUES (
    1, 1, 1, 1, 'Sample Project', 'audio',
    'Sample audio description for development demo.', NULL
)
ON DUPLICATE KEY UPDATE data_description = VALUES(data_description);
