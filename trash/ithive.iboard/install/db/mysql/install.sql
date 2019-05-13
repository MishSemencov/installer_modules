-- --------------------------------------------------------

--
-- Структура таблицы `ithive_iboard_boards`
--

CREATE TABLE IF NOT EXISTS `ithive_iboard_boards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `system` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ithive_iboard_categories`
--

CREATE TABLE IF NOT EXISTS `ithive_iboard_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL,
  `board_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `system` tinyint(1) NOT NULL,
  `incoming` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ithive_iboard_history`
--

CREATE TABLE IF NOT EXISTS `ithive_iboard_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `idea_id` int(11) NULL,
  `board_id` int(11) NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `type` text COLLATE utf8_unicode_ci NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ithive_iboard_ideas`
--

CREATE TABLE `ithive_iboard_ideas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `origin_id` int(11) NULL,
  `active` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sort_list` int(11) NOT NULL,
  `sort_table` int(11) NOT NULL,
  `min_list` tinyint(1) DEFAULT NULL,
  `min_table` tinyint(1) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci NULL,
  `category_id` int(11) NOT NULL,
  `board_id` int(11) NOT NULL,
  `important` tinyint(1) NOT NULL,
  `date_create` datetime NOT NULL,
  `date_update` datetime DEFAULT NULL,
  `date_visit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ithive_iboard_ideas_files`
--

CREATE TABLE IF NOT EXISTS `ithive_iboard_ideas_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `idea_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ithive_iboard_ideas_shared`
--

CREATE TABLE IF NOT EXISTS `ithive_iboard_ideas_shared` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idea_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ithive_iboard_ideas_subscription`
--

CREATE TABLE IF NOT EXISTS `ithive_iboard_ideas_subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idea_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ithive_iboard_ideas_tags`
--

CREATE TABLE IF NOT EXISTS `ithive_iboard_ideas_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idea_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ithive_iboard_reminders`
--

CREATE TABLE `ithive_iboard_reminders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_create` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `period` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idea_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ithive_iboard_tags`
--

CREATE TABLE IF NOT EXISTS `ithive_iboard_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

