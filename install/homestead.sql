-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 2018-08-18 15:43:14
-- 服务器版本： 10.2.14-MariaDB
-- PHP Version: 7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `homestead`
--

-- --------------------------------------------------------

--
-- 表的结构 `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `migration` varchar(255) NOT NULL COMMENT '迁移',
  `batch` int(11) NOT NULL COMMENT '包',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='迁移';

--
-- 转存表中的数据 `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2018_08_06_042511_create_view_table', 1);

-- --------------------------------------------------------

--
-- 表的结构 `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `view`
--

DROP TABLE IF EXISTS `view`;
CREATE TABLE IF NOT EXISTS `view` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `view_name` varchar(255) DEFAULT NULL COMMENT '名称',
  `desc` varchar(255) DEFAULT NULL COMMENT '描述',
  `TABLE_NAME` varchar(255) DEFAULT NULL COMMENT '所属表',
  `type` varchar(255) DEFAULT NULL COMMENT '类型',
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '配置',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='视图';

--
-- 转存表中的数据 `view`
--

INSERT INTO `view` (`id`, `view_name`, `desc`, `TABLE_NAME`, `type`, `json`) VALUES
(1, 'table', '展示表格的表格配置', 'INFORMATION_SCHEMA.TABLES', 'table', '{\r\n    \"tools\":[{\r\n        \"name\":\"add\",\r\n        \"buttonType\":\"primary\",\r\n        \"type\":\"elementButton\",\r\n        \"icon\":\"el-icon-circle-plus-outline\"\r\n    },{\r\n        \"name\":\"import\",\r\n        \"type\":\"elementButton\",\r\n        \"icon\":\"el-icon-upload2\"\r\n    },{\r\n        \"name\":\"export\",\r\n        \"type\":\"elementButton\",\r\n        \"icon\":\"el-icon-download\"\r\n    }],\r\n    \"form\":{\r\n        \"field\":\"TABLE_COMMENT\",\r\n        \"operator\": \"like\",\r\n        \"value\":\"\"\r\n    },\r\n    \"table\":{\r\n        \"border\":true,\r\n        \"stripe\":true,\r\n        \"defaultSort\":{\r\n            \"prop\":\"TABLE_COMMENT\",\r\n            \"order\":\"ascending\"\r\n        },\r\n        \"defaultSearch\":{\r\n            \"value\": \"TABLE_COMMENT\",\r\n            \"label\":\"表名\",\r\n            \"type\":\"datetime\",\r\n            \"sortable\": true,\r\n            \"fixed\": true,\r\n            \"resizable\": true\r\n        }, \r\n        \"fields\":[{\r\n            \"value\": \"TABLE_COMMENT\",\r\n            \"label\":\"表名\",\r\n            \"type\":\"datetime\",\r\n            \"sortable\": true,\r\n            \"fixed\": true,\r\n            \"resizable\": true\r\n        },{\r\n            \"value\": \"TABLE_NAME\",\r\n            \"label\":\"别名\",\r\n            \"type\":\"inputNumber\",\r\n            \"sortable\": true,\r\n            \"fixed\": false,\r\n            \"resizable\": true\r\n        },{\r\n            \"value\":\"desc\",\r\n            \"label\":\"备注\",\r\n            \"fixed\": false,\r\n            \"resizable\":false,\r\n            \"width\": \"400\"\r\n        }],            \r\n        \"operator\":[{\r\n            \"text\":\"字段\",\r\n            \"query\":{\"view_name\":\"field\",\"TABLE_NAME\":\"INFORMATION_SCHEMA.COLUMNS\"},\r\n            \"script\":\"this.addTab({name:\'table_field-\'+row.TABLE_NAME,title:row.TABLE_COMMENT+\'-字段\',content:\'tableVue\',query:Object.assign({where:{field:\'TABLE_NAME\',operator:\'=\',value:row.TABLE_NAME}},row,operator.query)});\"\r\n        },{\r\n            \"text\":\"视图\",\r\n			\"query\":{\"view_name\":\"view\",\"TABLE_NAME\":\"view\"},\r\n            \"script\":\"this.addTab({name:\'table_view-\'+row.TABLE_NAME,title:row.TABLE_COMMENT+\'-视图\',content:\'tableVue\',query:Object.assign({where:{field:\'TABLE_NAME\',operator:\'=\',value:row.TABLE_NAME}},row,operator.query)});\"\r\n        },{\r\n            \"text\":\"数据\",\r\n            \"query\":{\"view_name\":\"\"},\r\n            \"script\":\"this.addTab({name:\'table_data-\'+row.TABLE_NAME,title:row.TABLE_COMMENT+\'-数据\',content:\'tableVue\',query:Object.assign({},row,operator.query)});\"\r\n        }]\r\n    },     \r\n    \"pagination\":{\r\n        \"currentPage\":1,\r\n        \"pageSize\":10\r\n    }    \r\n}'),
(2, 'field', '展示字段的表格配置', 'INFORMATION_SCHEMA.COLUMNS', 'table', '{\r\n    \"tools\":[{\r\n        \"name\":\"add\",\r\n        \"buttonType\":\"primary\",\r\n        \"type\":\"elementButton\",\r\n        \"icon\":\"el-icon-circle-plus-outline\"\r\n    },{\r\n        \"name\":\"import\",\r\n        \"type\":\"elementButton\",\r\n        \"icon\":\"el-icon-upload2\"\r\n    },{\r\n        \"name\":\"export\",\r\n        \"type\":\"elementButton\",\r\n        \"icon\":\"el-icon-download\"\r\n    }],\r\n    \"form\":{\r\n        \"field\":\"COLUMN_COMMENT\",\r\n        \"operator\": \"like\",\r\n        \"value\":\"\"\r\n    },\r\n    \"table\":{\r\n        \"border\":true,\r\n        \"stripe\":true,\r\n        \"defaultSort\":{\r\n            \"prop\":\"COLUMN_COMMENT\",\r\n            \"order\":\"ascending\"\r\n        },\r\n        \"defaultSearch\":{\r\n            \"value\": \"COLUMN_COMMENT\",\r\n            \"label\":\"字段名\",\r\n            \"type\":\"text\",\r\n            \"sortable\": true,\r\n            \"fixed\": true,\r\n            \"resizable\": true\r\n        }, \r\n        \"fields\":[{\r\n            \"value\": \"COLUMN_COMMENT\",\r\n            \"label\":\"字段名\",\r\n            \"type\":\"text\",\r\n            \"sortable\": true,\r\n            \"fixed\": true,\r\n            \"resizable\": true\r\n        },{\r\n            \"value\": \"COLUMN_NAME\",\r\n            \"label\":\"字段别名\",\r\n            \"type\":\"text\",\r\n            \"sortable\": true,\r\n            \"fixed\": true,\r\n            \"resizable\": true\r\n        },{\r\n            \"value\":\"DATA_TYPE\",\r\n            \"label\":\"字段类型\",\r\n            \"type\":\"text\",\r\n            \"sortable\": true,\r\n            \"fixed\": false,\r\n            \"resizable\":false\r\n        },{\r\n            \"value\":\"CHARACTER_MAXIMUM_LENGTH\",\r\n            \"label\":\"长度\",\r\n            \"type\":\"inputNumber\",\r\n            \"sortable\": true,\r\n            \"fixed\": false,\r\n            \"resizable\":false\r\n        }],            \r\n        \"operator\":[{\r\n            \"text\":\"查看\",\r\n            \"script\":\"this.view(row,operator)\"\r\n        },{\r\n            \"text\":\"编辑\",\r\n            \"script\":\"this.edit(row,operator)\"\r\n        },{\r\n            \"text\":\"删除\",\r\n            \"script\":\"this.delete(row,operator)\"\r\n        }]\r\n    },     \r\n    \"pagination\":{\r\n        \"currentPage\":1,\r\n        \"pageSize\":10\r\n    }    \r\n}'),
(3, 'view', '展示视图的表格配置', 'view', 'table', '{\r\n    \"tools\":[{\r\n        \"name\":\"add\",\r\n        \"buttonType\":\"primary\",\r\n        \"type\":\"elementButton\",\r\n        \"icon\":\"el-icon-circle-plus-outline\",\r\n        \"query\":{\"view\":\"视图添加\"},\r\n        \"script\":\"this.add(event,config)\"\r\n    },{\r\n        \"name\":\"import\",\r\n        \"type\":\"elementButton\",\r\n        \"icon\":\"el-icon-upload2\"\r\n    },{\r\n        \"name\":\"export\",\r\n        \"type\":\"elementButton\",\r\n        \"icon\":\"el-icon-download\"\r\n    }],\r\n    \"form\":{\r\n        \"field\":\"view_name\",\r\n        \"operator\": \"like\",\r\n        \"value\":\"\"\r\n    },\r\n    \"table\":{\r\n        \"border\":true,\r\n        \"stripe\":true,\r\n        \"defaultSort\":{\r\n            \"prop\":\"view_name\",\r\n            \"order\":\"ascending\"\r\n        },\r\n        \"defaultSearch\":{\r\n            \"value\": \"view_name\",\r\n            \"label\":\"名称\",\r\n            \"type\":\"text\",\r\n            \"sortable\": true,\r\n            \"fixed\": true,\r\n            \"resizable\": true,\r\n            \"showOverflowTooltip\":true\r\n        }, \r\n        \"fields\":[{\r\n            \"value\": \"id\",\r\n            \"label\":\"id\",\r\n            \"type\":\"text\",\r\n            \"sortable\": true,\r\n            \"fixed\": true,\r\n            \"resizable\": true,\r\n            \"width\": 60,\r\n            \"showOverflowTooltip\":true\r\n        },{\r\n            \"value\": \"view_name\",\r\n            \"label\":\"名称\",\r\n            \"type\":\"text\",\r\n            \"sortable\": true,\r\n            \"fixed\": true,\r\n            \"resizable\": true,\r\n            \"showOverflowTooltip\":true\r\n        },{\r\n            \"value\": \"desc\",\r\n            \"label\":\"描述\",\r\n            \"type\":\"text\",\r\n            \"sortable\": true,\r\n            \"fixed\": true,\r\n            \"resizable\": true,\r\n            \"showOverflowTooltip\":true\r\n        },{\r\n            \"value\":\"TABLE_NAME\",\r\n            \"label\":\"所属表\",\r\n            \"type\":\"text\",\r\n            \"sortable\": true,\r\n            \"fixed\": false,\r\n            \"resizable\":false,\r\n            \"showOverflowTooltip\":true\r\n        },{\r\n            \"value\":\"type\",\r\n            \"label\":\"类型\",\r\n            \"type\":\"text\",\r\n            \"sortable\": true,\r\n            \"fixed\": false,\r\n            \"resizable\":false,\r\n            \"showOverflowTooltip\":true\r\n        },{\r\n            \"value\":\"json\",\r\n            \"label\":\"配置\",\r\n            \"type\":\"textarea\",\r\n            \"sortable\": true,\r\n            \"fixed\": false,\r\n            \"resizable\":false,\r\n            \"showOverflowTooltip\":true\r\n        }],            \r\n        \"operator\":[{\r\n            \"text\":\"查看\",\r\n            \"script\":\"this.view(row,operator)\"\r\n        },{\r\n            \"text\":\"编辑\",\r\n            \"script\":\"this.edit(row,operator)\"\r\n        },{\r\n            \"text\":\"删除\",\r\n            \"script\":\"this.delete(row,operator)\"\r\n        }]\r\n    },     \r\n    \"pagination\":{\r\n        \"currentPage\":1,\r\n        \"pageSize\":10\r\n    }    \r\n}'),
(4, '视图-添加', '视图表的添加视图', 'view', 'form', '{\"fields\":[{\"value\":\"id\",\"name\":\"id\",\"label\":\"id\",\"max\":\"\",\"fixed\":0,\"resizable\":1,\"sortable\":1,\"showOverflowTooltip\":1,\"type\":\"elementText\"},{\"value\":\"view_name\",\"name\":\"view_name\",\"label\":\"名称\",\"max\":\"\",\"fixed\":0,\"resizable\":1,\"sortable\":1,\"showOverflowTooltip\":1,\"type\":\"elementText\"},{\"value\":\"desc\",\"name\":\"desc\",\"label\":\"描述\",\"max\":\"\",\"fixed\":0,\"resizable\":1,\"sortable\":1,\"showOverflowTooltip\":1,\"type\":\"elementText\"},{\"value\":\"TABLE_NAME\",\"name\":\"TABLE_NAME\",\"label\":\"所属表\",\"max\":\"\",\"fixed\":0,\"resizable\":1,\"sortable\":1,\"showOverflowTooltip\":1,\"type\":\"elementText\",\"script\":\"if(binding.value.query.TABLE_NAME){binding.value.field.readonly=true;binding.value.form.TABLE_NAME=binding.value.query.TABLE_NAME}\"},{\"value\":\"type\",\"name\":\"type\",\"label\":\"类型\",\"max\":\"\",\"fixed\":0,\"resizable\":1,\"sortable\":1,\"showOverflowTooltip\":1,\"type\":\"elementText\"},{\"value\":\"json\",\"name\":\"json\",\"label\":\"配置\",\"max\":\"\",\"fixed\":0,\"resizable\":1,\"sortable\":1,\"showOverflowTooltip\":1,\"type\":\"elementTextarea\",\"autosize\":{\"minRows\":5},\"placeholder\":\"{\\r\\n\\\"配置方式\\\":{\\r\\n    \\\"1\\\":\\\"在此以json格式手动配置好确定保存\\\",\\r\\n    \\\"2\\\":\\\"点击配置进入下一步以拖拽方式配置\\\"\\r\\n}}\"}],\"tools\":[{\"buttonType\":\"primary\",\"text\":\"配置\",\"operator\":\"elementSelect\",\"query\":[],\"script\":\"this.config(event,config)\"},{\"buttonType\":\"primary\",\"shape\":\"plain\",\"text\":\"确定\",\"query\":[],\"script\":\"this.save(event,config)\"},{\"text\":\"取消\",\"query\":[],\"script\":\"this.cancel(event,config)\"}],\"form\":{\"view_name\":\"视图-查看\",\"desc\":\"\",\"type\":\"form\",\"TABLE_NAME\":\"\",\"json\":\"\"}}'),
(5, '视图-查看', '视图表的查看视图', 'view', 'form', NULL),
(6, '视图-编辑', '视图表的编辑视图', 'view', 'form', '{\"fields\":[{\"type\":\"elementText\",\"label\":\"id\",\"name\":\"id\",\"readonly\":true},{\"type\":\"elementText\",\"label\":\"名称\",\"name\":\"view_name\"},{\"type\":\"elementText\",\"label\":\"描述\",\"name\":\"desc\"},{\"type\":\"elementText\",\"label\":\"所属表\",\"name\":\"TABLE_NAME\"},{\"type\":\"elementSelect\",\"label\":\"类型\",\"name\":\"type\",\"source\":\"options\",\"options\":[{\"value\":\"table\",\"label\":\"表格\"},{\"value\":\"form\",\"label\":\"表单\"},{\"value\":\"view\",\"label\":\"视图\"}],\"tableField\":{\"table\":\"\",\"fieldLabel\":\"\",\"fieldValue\":\"\"}},{\"type\":\"elementTextarea\",\"label\":\"多行文本\",\"name\":\"json\",\"autosize\":{\"minRows\":5},\"placeholder\":\"{\\n\\\"配置方式\\\":{\\n    \\\"1\\\":\\\"在此以json格式手动配置好确定保存\\\",\\n    \\\"2\\\":\\\"点击配置进入下一步以拖拽方式配置\\\"\\n}}\"}],\"form\":{\"sample\":\"\",\"json\":\"\"},\"tools\":[{\"type\":\"elementButton\",\"text\":\"保存并关闭\",\"shape\":\"\",\"buttonType\":\"primary\",\"name\":\"button\",\"title\":\"保存并关闭\",\"operator\":\"elementSelect\",\"script\":\"this.save(event,config,true);\"},{\"type\":\"elementButton\",\"text\":\"保存\",\"shape\":\"plain\",\"buttonType\":\"primary\",\"name\":\"button\",\"title\":\"保存\",\"operator\":\"elementSelect\",\"script\":\"this.save(event,config);\"},{\"type\":\"elementButton\",\"text\":\"取消\",\"shape\":\"\",\"buttonType\":\"\",\"name\":\"button\",\"title\":\"取消\",\"operator\":\"elementSelect\",\"script\":\"this.cancel(event,config);\"}]}');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
