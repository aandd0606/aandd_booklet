CREATE TABLE `book` (
  `book_sn` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '書籍序號',
  `book_title` varchar(255) NOT NULL COMMENT '書籍標題',
  `book_content` text NOT NULL COMMENT '書籍簡介',
  `book_keyword` varchar(255) NOT NULL COMMENT '書籍關鍵字',
  `book_date` date NOT NULL COMMENT '出版日期',
  `book_click` mediumint(8) unsigned NOT NULL COMMENT '書籍點閱次數',
  `book_enable` enum('yes','no') NOT NULL DEFAULT 'no' COMMENT '是否開放閱讀',
  PRIMARY KEY (`book_sn`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE `page` (
  `page_sn` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '頁面序號',
  `page_title` varchar(255) NOT NULL COMMENT '頁面標題',
  `page_content` text NOT NULL COMMENT '頁面內容',
  `page_sort` tinyint(3) unsigned NOT NULL COMMENT '頁面排序',
  `book_sn` smallint(5) unsigned NOT NULL COMMENT '書籍序號',
  PRIMARY KEY (`page_sn`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE `comment` (
  `comment_sn` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '留言序號',
  `book_sn` smallint(5) unsigned NOT NULL COMMENT '書籍序號',
  `comment_content` varchar(255) NOT NULL COMMENT '留言內容',
  `comment_ip` varchar(255) NOT NULL COMMENT '留言ip',
  `comment_time` datetime NOT NULL COMMENT '留言時間',
  PRIMARY KEY (`comment_sn`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE `rubric` (
  `rubric_sn` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '評分序號',
  `book_sn` smallint(5) unsigned NOT NULL COMMENT '書籍序號',
  `rubric_type` varchar(255) NOT NULL COMMENT '評分類別',
  `rubric_val` tinyint(3) unsigned NOT NULL COMMENT '評分分數',
  PRIMARY KEY (`rubric_sn`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;