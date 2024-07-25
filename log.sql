CREATE TABLE `canal_change_log` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `schema` varchar(64) NOT NULL DEFAULT '',
    `table` varchar(64) NOT NULL DEFAULT '',
    `rows` text DEFAULT NULL,
    `status` varchar(32) NOT NULL DEFAULT '',
    `event_type` varchar(10) NOT NULL DEFAULT '',
    `create_time` datetime DEFAULT NULL,
    `finish_time` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;