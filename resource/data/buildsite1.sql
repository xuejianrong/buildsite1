/*
Navicat MySQL Data Transfer

Source Server         : Centos6.2
Source Server Version : 50639
Source Host           : 192.168.0.168:3306
Source Database       : buildsite1

Target Server Type    : MYSQL
Target Server Version : 50639
File Encoding         : 65001

Date: 2018-06-12 11:49:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `content_item`
-- ----------------------------
DROP TABLE IF EXISTS `content_item`;
CREATE TABLE `content_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `managerId` int(11) NOT NULL COMMENT '发表管理员ID',
  `type` int(11) NOT NULL COMMENT '内容类型：1客户案例',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `source` varchar(50) NOT NULL COMMENT '来源',
  `content` text NOT NULL COMMENT '内容',
  `other_info` text NOT NULL COMMENT '其它信息json',
  `order` int(11) NOT NULL COMMENT '排序',
  `is_jinping` tinyint(4) NOT NULL COMMENT '是否精品',
  `status` int(11) NOT NULL COMMENT '状态：0未发布1发布2删除',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of content_item
-- ----------------------------
INSERT INTO `content_item` VALUES ('1', '1', '1', '保定市卓生医疗器械制造有限公司', 'admin', '<p><span style=\"color: rgb(0, 0, 0); font-family: 微软雅黑; text-align: center;\">保定市卓生医疗器械制造有限公司</span><span style=\"color: rgb(0, 0, 0); font-family: 微软雅黑; text-align: center;\">保定市卓生医疗器械制造有限公司</span><span style=\"color: rgb(0, 0, 0); font-family: 微软雅黑; text-align: center;\">保定市卓生医疗器械制造有限公司</span><span style=\"color: rgb(0, 0, 0); font-family: 微软雅黑; text-align: center;\">保定市卓生医疗器械制造有限公司</span><span style=\"color: rgb(0, 0, 0); font-family: 微软雅黑; text-align: center;\">保定市卓生医疗器械制造有限公司</span><span style=\"color: rgb(0, 0, 0); font-family: 微软雅黑; text-align: center;\">保定市卓生医疗器械制造有限公司</span><span style=\"color: rgb(0, 0, 0); font-family: 微软雅黑; text-align: center;\">保定市卓生医疗器械制造有限公司</span><span style=\"color: rgb(0, 0, 0); font-family: 微软雅黑; text-align: center;\">保定市卓生医疗器械制造有限公司</span><span style=\"color: rgb(0, 0, 0); font-family: 微软雅黑; text-align: center;\">保定市卓生医疗器械制造有限公司</span><span style=\"color: rgb(0, 0, 0); font-family: 微软雅黑; text-align: center;\">保定市卓生医疗器械制造有限公司</span></p>', '{\"url\":\"http:\\/\\/www.bdzhuosheng.com\",\"shortcut\":\"data\\/uploads\\/19\\/27c3c8864bb74c22ae924ec6b82d18ae.jpg\"}', '3', '0', '1', '1527831789');
INSERT INTO `content_item` VALUES ('2', '1', '1', '南京熙健信息技术有限公司', 'admin', '<p><span style=\"color: rgb(102, 102, 102); font-family: 微软雅黑; text-indent: 32px;\">迈瑞成立于1991年，是全球领先的医疗设备与解决方案供应商。迈瑞总部设在中国深圳，在北美、欧洲、亚洲、非洲、拉美等地区的32个国家设有42家子公司，在中国设有32家分公司，雇员近10000名，形成了庞大的全球研发、营销和服务网络。迈瑞融合创新，紧贴临床需求，帮助世界各地人们改善医疗条件、降低医疗成本。目前，迈瑞的产品与解决方案已应用于全球190多个国家，中国近11万家医疗机构和95%以上的三甲医院。前端通过平缓以及流畅的交互效果，使用户自主参与到页面的互动中，除了辅助作用之外，还能引导用户快速的找到他们想要的信息，提升了网站整体的用户体验。</span></p>', '{\"url\":\"http:\\/\\/www.mhealth365.com\",\"shortcut\":\"data\\/uploads\\/90\\/0294cea88858a11d364d73328fb9ecd4.jpg\"}', '1', '1', '1', '1527832910');
INSERT INTO `content_item` VALUES ('3', '1', '1', '上海贝瑞电子科技有限公司', 'admin', '<p>上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司上海贝瑞电子科技有限公司</p>', '{\"url\":\"http:\\/\\/www.berry-med.com\",\"shortcut\":\"data\\/uploads\\/10\\/0431ab7f01cbeb06973bdcd0e5739f1a.jpg\"}', '2', '0', '1', '1527833224');

-- ----------------------------
-- Table structure for `manager`
-- ----------------------------
DROP TABLE IF EXISTS `manager`;
CREATE TABLE `manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `nick_name` varchar(30) NOT NULL COMMENT '姓名',
  `account` varchar(50) NOT NULL COMMENT '账号',
  `password` varchar(50) NOT NULL COMMENT '密码',
  `group_id` int(11) NOT NULL COMMENT '用户组ID',
  `is_forbidden` tinyint(4) NOT NULL COMMENT '是否禁用',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of manager
-- ----------------------------
INSERT INTO `manager` VALUES ('1', '超级管理员', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '1', '0', '1527672186');
INSERT INTO `manager` VALUES ('2', 'rjin', 'rjin', 'e10adc3949ba59abbe56e057f20f883e', '2', '0', '1528100615');

-- ----------------------------
-- Table structure for `manager_group`
-- ----------------------------
DROP TABLE IF EXISTS `manager_group`;
CREATE TABLE `manager_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `name` varchar(30) NOT NULL COMMENT '组名称',
  `actions` text NOT NULL COMMENT '有权限访问的action',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of manager_group
-- ----------------------------
INSERT INTO `manager_group` VALUES ('1', '管理员', '[\"manager\\/index\",\"manager\\/save\",\"manager\\/update\",\"manager\\/delete\",\"manager-group\\/index\",\"manager-group\\/save\",\"manager-group\\/update\",\"manager-group\\/delete\",\"manager-group\\/edit-actions\",\"manager-group\\/save-actions\",\"site-setting\\/index\",\"site-setting\\/save\",\"products-category\\/index\",\"products-category\\/save\",\"products-category\\/delete\",\"products\\/index\",\"products\\/save\",\"products\\/delete\",\"news-category\\/index\",\"news-category\\/save\",\"news-category\\/delete\",\"news\\/index\",\"news\\/save\",\"news\\/delete\",\"zhaopin\\/index\",\"zhaopin\\/save\",\"zhaopin\\/delete\",\"zhaopin\\/talent-concept\",\"zhaopin\\/save-talent-concept\",\"contactus\\/index\",\"contactus\\/save\",\"aboutus\\/index\",\"aboutus\\/save\",\"site-message\\/index\",\"site-message\\/delete\",\"upload\\/upload-image\"]');
INSERT INTO `manager_group` VALUES ('2', '业务员', '[\"site-setting\\/index\",\"products-category\\/index\",\"products\\/index\",\"news-category\\/index\",\"news\\/index\",\"zhaopin\\/index\",\"zhaopin\\/talent-concept\",\"contactus\\/index\",\"aboutus\\/index\",\"site-message\\/index\"]');

-- ----------------------------
-- Table structure for `news`
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `category_id` int(11) NOT NULL COMMENT '分类ID',
  `shortcut` varchar(100) NOT NULL COMMENT '新闻图片',
  `title` varchar(100) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `click_count` int(11) NOT NULL COMMENT '点击次数',
  `status` tinyint(4) NOT NULL COMMENT '状态：0未发布1已发布',
  `publish_time` int(11) NOT NULL COMMENT '发布时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of news
-- ----------------------------
INSERT INTO `news` VALUES ('1', '1', 'data/uploads/96/298558195a1b1c55119e65ea88418567.jpg', '珠海黑马即将参加2015第74届中国国际医疗器械（秋季）博览会', '<p><span style=\"color: rgb(0, 0, 0); font-family: 宋体; font-size: 14px; text-indent: 28px;\">中国国际医疗器械博览会（CMEF），始创于1979年，每年春秋两届，在经历了30多年的不断创新、自我完善，已成为亚太地区最大的医疗器械及相关产品、服务展览会。展出内容全面涵盖了包括医用影像、体外诊断、电子、光学、急救、康复护理以及医疗信息技术、外包服务等上万种产品，直接并全面服务于医疗器械行业从源头到终端整条医疗产业链。每一届来自20 多个国家的2700余家医疗器械生产企业和超过全球100多个国家和地区的150000人次的政府机构采购、医院买家和经销商汇聚CMEF交易、交流；随着展览会更加专业化的纵深发展，先后创立了会议论坛、 CMEF Imaging、CMEF IVD、CMEF IT、CMEF Orthopedics 以及ICMD一系列的医疗领域子品牌，CMEF已成为医疗行业内最大的专业医疗采购贸易平台、最佳的企业形象发布地以及专业信息集散地和学术、 技术交流平台。</span></p><p><img src=\"http://www.buildsite1.test/resource/data/uploads/72/b65b62dd56b2c3159d09b4473588bc08.jpg\" _src=\"http://www.buildsite1.test/resource/data/uploads/72/b65b62dd56b2c3159d09b4473588bc08.jpg\" style=\"max-width: 100%;\"></p><p><span style=\"color: rgb(0, 0, 0); font-family: 宋体; font-size: 14px; text-indent: 28px;\">我司在此次展会上将展示的产品包括：气道清除系统、红外偏振光治疗仪、亚低温治疗仪、振动式物理治疗仪、空气波压力治疗仪、牙科手机清洗机、电动气压止血仪等临床医疗器械以及器皿清洗机、离心机、基因扩增仪等实验室设备产品。</span></p>', '0', '1', '1528424302', '1528424215');
INSERT INTO `news` VALUES ('2', '2', 'data/uploads/40/7a219dd43a712dc63433e55e3b1b130c.jpg', '“对客户好总是对的”黑马公司总经理组织内部产品培训', '<p>“对客户好总是对的”黑马公司总经理组织内部产品培训“对客户好总是对的”黑马公司总经理组织内部产品培训“对客户好总是对的”黑马公司总经理组织内部产品培训“对客户好总是对的”黑马公司总经理组织内部产品培训“对客户好总是对的”黑马公司总经理组织内部产品培训“对客户好总是对的”黑马公司总经理组织内部产品培训“对客户好总是对的”黑马公司总经理组织内部产品培训“对客户好总是对的”黑马公司总经理组织内部产品培训“对客户好总是对的”黑马公司总经理组织内部产品培训“对客户好总是对的”黑马公司总经理组织内部产品培训“对客户好总是对的”黑马公司总经理组织内部产品培训</p>', '0', '1', '1528424514', '1528424514');

-- ----------------------------
-- Table structure for `news_category`
-- ----------------------------
DROP TABLE IF EXISTS `news_category`;
CREATE TABLE `news_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `pid` int(11) NOT NULL COMMENT '父节点ID',
  `name` varchar(50) NOT NULL COMMENT '新闻分类名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of news_category
-- ----------------------------
INSERT INTO `news_category` VALUES ('1', '0', '企业新闻');
INSERT INTO `news_category` VALUES ('2', '0', '行业新闻');
INSERT INTO `news_category` VALUES ('3', '0', '媒体报道');
INSERT INTO `news_category` VALUES ('4', '0', '技术资讯');

-- ----------------------------
-- Table structure for `products`
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `category_id` int(11) NOT NULL COMMENT '分类ID',
  `name` varchar(100) NOT NULL COMMENT '产品名称',
  `shortcut` varchar(100) NOT NULL COMMENT '产品图片',
  `product_model` varchar(30) NOT NULL COMMENT '产品型号',
  `produce_place` varchar(50) NOT NULL COMMENT '原产地',
  `brand` varchar(30) NOT NULL COMMENT '商标品牌',
  `price` varchar(20) NOT NULL COMMENT '参考价格',
  `delivery_address` varchar(50) NOT NULL COMMENT '交货地点',
  `has_sample` tinyint(4) NOT NULL COMMENT '是否提供样品',
  `description` varchar(500) NOT NULL COMMENT '产品描述',
  `other_info` text NOT NULL COMMENT '产品其它信息json',
  `status` tinyint(4) NOT NULL COMMENT '状态：0未发布1已发布',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of products
-- ----------------------------
INSERT INTO `products` VALUES ('1', '1', '红外偏振光治疗仪', 'data/uploads/45/568b514b777d41a9a6bdcb248d2e4761.jpg', 'H2', '广东省 珠海市', '黑马', '详情请电话咨询', '指定地点', '0', '珠海黑马公司红外偏振光治疗仪，是采用国际先进光电技术，产生600-1600nm的点式直线红外偏振光，最大光波输出功率高达5600mw，对人体组织的有效作用深度可达5cm以上，适用于软组织扭挫伤恢复期改善局部血液循环，促进肿胀消退和镇痛。', '[{\"key\":\"\\u5149\\u8c31\\u8303\\u56f4\",\"value\":\"600-1600nm\"},{\"key\":\"\\u4e2d\\u5fc3\\u5cf0\\u503c\\u6ce2\\u957f\",\"value\":\"\\u7ea61100nm\"},{\"key\":\"\\u590d\\u5408\\u6ce2\\u957f\\u8303\\u56f4\",\"value\":\"600-1600nm\"},{\"key\":\"\\u504f\\u632f\\u5149\",\"value\":\"\\u7ebf\\u6027\\u504f\\u632f\\u5149\"},{\"key\":\"\\u6700\\u5927\\u8f93\\u51fa\\u5149\\u529f\\u7387\",\"value\":\"\\u22655600mw\"},{\"key\":\"\\u5149\\u6e90\",\"value\":\"\\u65e5\\u672c\\u539f\\u88c5\\u5bbd\\u5149\\u8c31\\u91d1\\u5364\\u706f\\uff0c150W\\u00d72\"},{\"key\":\"\\u5149\\u7ea4\",\"value\":\"\\u9ad8\\u5bfc\\u901a\\u7387\\u76f4\\u7ebf\\u5149\\u7ea4\"},{\"key\":\"\\u63a7\\u5236\\u65b9\\u5f0f\",\"value\":\"\\u53cc\\u7167\\u5c04\\u5934\\u5de5\\u4f5c\\uff0c\\u53c2\\u6570\\u53ef\\u5355\\u72ec\\u8c03\\u6574\"},{\"key\":\"\\u663e\\u793a\\u65b9\\u5f0f\",\"value\":\"\\u53cc\\u5c4f\\u4e2d\\u6587LCD\\u9ad8\\u6e05\\u663e\\u793a\\uff0c\\u64cd\\u4f5c\\u66f4\\u7b80\\u4fbf\"},{\"key\":\"\\u6cbb\\u7597\\u6a21\\u5f0f\",\"value\":\"\\u5b89\\u5168\\u3001\\u8fde\\u7eed\\u3001\\u95f4\\u9694\\u3001\\u529f\\u7387\\u4f18\\u5148\\u3001\\u65f6\\u95f4\\u4f18\\u5148\\u7b495\\u79cd\\u6a21\\u5f0f\\uff0c100\\u591a\\u79cd\\u7ec4\\u5408\"},{\"key\":\"\\u5149\\u529f\\u7387\\u8c03\\u8282\",\"value\":\"10-100%\\u8fde\\u7eed\\u53ef\\u8c03\"},{\"key\":\"\\u6cbb\\u7597\\u65f6\\u95f4\",\"value\":\"1-10\\u5206\\u949f\\u8fde\\u7eed\\u53ef\\u8c03\"},{\"key\":\"\\u7167\\u5c04\\u5934\\u89c4\\u683c\",\"value\":\"B1\\u3001SG\\u3001C\\u3001D\\u7b49\"}]', '1', '1528437297');
INSERT INTO `products` VALUES ('2', '2', 'G1000振动排痰机', 'data/uploads/51/8a258f8025344e0e1bba3661b1c2cfd1.jpg', 'G1000', '法国', 'kucii', '10000', '中国', '0', 'G1000振动排痰机G1000振动排痰机G1000振动排痰机', '[{\"key\":\"\\u6cbb\\u7597\\u65f6\\u95f4\",\"value\":\"1-10\\u5206\\u949f\\u8fde\\u7eed\\u53ef\\u8c03\"},{\"key\":\"\\u6700\\u5927\\u8f93\\u51fa\\u5149\\u529f\\u7387\",\"value\":\"\\u22655600mw\"}]', '1', '1528437635');
INSERT INTO `products` VALUES ('3', '3', '32414', 'data/uploads/43/5062f6c8783b6395f349ee68843368a1.jpg', '324', '423', '4324', '234', '234', '1', 'gfdsgdsfgd', '[{\"key\":\"dfsg\",\"value\":\"sdfg\"},{\"key\":\"dsg\",\"value\":\"sdfgsd\"}]', '1', '1528709675');

-- ----------------------------
-- Table structure for `products_category`
-- ----------------------------
DROP TABLE IF EXISTS `products_category`;
CREATE TABLE `products_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `pid` int(11) NOT NULL COMMENT '父节点ID',
  `name` varchar(50) NOT NULL COMMENT '产品分类名称',
  `shortcut` varchar(100) NOT NULL COMMENT '图片',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of products_category
-- ----------------------------
INSERT INTO `products_category` VALUES ('1', '0', '临床医疗设备', 'data/uploads/55/c6b4de15b6f7ce2c664e84c869ba8156.jpg');
INSERT INTO `products_category` VALUES ('2', '0', '实验室设备', 'data/uploads/29/906bccc791ad5fddf59e9fb3b279af7d.jpg');
INSERT INTO `products_category` VALUES ('3', '0', '公安法医系列设备', 'data/uploads/72/ae4ee3ee519c69cec6b8a2828e80b7c5.jpg');

-- ----------------------------
-- Table structure for `site_message`
-- ----------------------------
DROP TABLE IF EXISTS `site_message`;
CREATE TABLE `site_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `contact_name` varchar(30) NOT NULL COMMENT '联系姓名',
  `tel` varchar(20) NOT NULL COMMENT '电话',
  `email` varchar(50) NOT NULL COMMENT '邮箱',
  `company_name` varchar(100) NOT NULL COMMENT '联系单位',
  `address` varchar(100) NOT NULL COMMENT '联系地址',
  `content` varchar(255) NOT NULL COMMENT '内容',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of site_message
-- ----------------------------
INSERT INTO `site_message` VALUES ('1', '0', '李生', '15012121448', '7958@qq.com', 'sfs', '', 'sdfsfsdd', '1528080721');
INSERT INTO `site_message` VALUES ('2', '0', '黄生', '15012121447', '7958@qq.com', '顶替', '', '工奇才', '1528080830');
INSERT INTO `site_message` VALUES ('3', '0', '老大爷', '15014147445', 'sfsd@qq.com', 'sdfsd', '', 'sdfsdf', '1528081404');
INSERT INTO `site_message` VALUES ('4', '0', 'dsf', '13710346292', '454', '456', '', 'fsdfdsgsdfg', '1528178534');
INSERT INTO `site_message` VALUES ('5', '0', '曼国', '020-556545', 'sdf@ff.cof', '', '标准32', '1402151889', '1528706311');
