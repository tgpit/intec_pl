CREATE TABLE IF NOT EXISTS `vkapi_market_access_list`
(
    `ID`           INT(11)      NOT NULL AUTO_INCREMENT,
    `USER_ID`      INT(11)      NOT NULL,
    `USER_ID_VK`   INT(11)      NOT NULL,
    `EXPIRES_IN`   DATETIME     NOT NULL,
    `ACCESS_TOKEN` VARCHAR(255) NOT NULL,
    `NAME`         VARCHAR(255) NOT NULL,
    PRIMARY KEY (`ID`),
    KEY `ix_uid` (`USER_ID`),
    KEY `ix_userid` (`USER_ID_VK`)
);

CREATE TABLE IF NOT EXISTS `vkapi_market_anticaptcha_list`
(
    `ID`          INT(11)     NOT NULL AUTO_INCREMENT,
    `CID`         VARCHAR(20) NOT NULL,
    `WORD`        VARCHAR(30) NOT NULL,
    `STATUS`      VARCHAR(1)  NOT NULL DEFAULT '0',
    `TIME_CREATE` DATETIME    NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE IF NOT EXISTS `vkapi_market_export_list`
(
    `ID`         INT(11)    NOT NULL AUTO_INCREMENT,
    `SITE_ID`    VARCHAR(2) NOT NULL,
    `ACCOUNT_ID` INT(11)    NOT NULL,
    `GROUP_ID`   INT(11)    NOT NULL,
    `GROUP_NAME` VARCHAR(255)        DEFAULT NULL,
    `NAME`       VARCHAR(255)        DEFAULT NULL,
    `ACTIVE`     TINYINT(1)          DEFAULT NULL,
    `CATALOG_ID` INT(11)    NOT NULL,
    `PARAMS`     LONGTEXT   NOT NULL,
    `ALBUMS`     LONGTEXT   NOT NULL,
    `AUTO`       tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`ID`)
);


CREATE TABLE IF NOT EXISTS `vkapi_market_export_item_list`
(
    `ID`         INT(11)    NOT NULL AUTO_INCREMENT,
    `EXPORT_ID`  INT(11)    NOT NULL,
    `ELEMENT_ID` INT(11)    NOT NULL,
    `ITEM_ID`    INT(11)             DEFAULT NULL,
    `STATUS`     TINYINT(1) NOT NULL DEFAULT '0',
    `PARENT_ID`  INT(11)             DEFAULT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `ix_element_id` (`ELEMENT_ID`, `EXPORT_ID`) USING BTREE,
    KEY `ix_eid_status` (`EXPORT_ID`, `STATUS`),
    KEY `ix_export_id_pid` (`EXPORT_ID`, `PARENT_ID`)
);

CREATE TABLE IF NOT EXISTS `vkapi_market_export_album_list`
(
    `ID`           INT(11)      NOT NULL AUTO_INCREMENT,
    `EXPORT_ID`    INT(11)      NOT NULL,
    `SECTION_ID`   INT(11)      NOT NULL,
    `ALBUM_ID`     INT(11)               DEFAULT NULL,
    `ALBUM_NAME`   VARCHAR(255) NOT NULL,
    `STATUS`       TINYINT(1)   NOT NULL DEFAULT '0',
    `ALBUM_CREATE` TINYINT(1)   NOT NULL DEFAULT '0',
    `SORT`         INT(11)      NOT NULL DEFAULT 0,
    PRIMARY KEY (`ID`),
    KEY `ix_eid_status` (`STATUS`, `EXPORT_ID`),
    KEY `ix_eid_secid` (`EXPORT_ID`, `SECTION_ID`)
);



CREATE TABLE IF NOT EXISTS `vkapi_market_export_photo_list`
(
    `ID`       int(11)      NOT NULL AUTO_INCREMENT,
    `FILE_ID`  int(11)      NOT NULL,
    `GROUP_ID` int(11)      NOT NULL,
    `PHOTO_ID` int(11)      NOT NULL,
    `MAIN`     tinyint(1)   NOT NULL DEFAULT '0',
    `PID`      int(11)      NOT NULL,
    `OID`      int(11)      NOT NULL,
    `HASH`     varchar(255) NOT NULL,
    `WM_HASH`  varchar(255)          DEFAULT NULL,
    PRIMARY KEY (`ID`),
    KEY `ix_photo_group` (`PHOTO_ID`, `GROUP_ID`) USING BTREE,
    KEY `ix_gid_pid_oid` (`GROUP_ID`, `PID`, `OID`) USING BTREE,
    KEY `ix_gid_fid_pid_oid` (`GROUP_ID`, `FILE_ID`, `PID`, `OID`) USING BTREE
);


CREATE TABLE IF NOT EXISTS `vkapi_market_log`
(
    `ID`          INT(11)    NOT NULL AUTO_INCREMENT,
    `EXPORT_ID`   INT(11)    NOT NULL,
    `TYPE`        TINYINT(1) NOT NULL,
    `CREATE_DATE` DATETIME   NOT NULL,
    `MSG`         TEXT       NOT NULL,
    `MORE`        TEXT,
    PRIMARY KEY (`ID`),
    KEY `ix_type` (`TYPE`, `EXPORT_ID`)
);


CREATE TABLE IF NOT EXISTS `vkapi_market_param`
(
    `CODE`      varchar(255) NOT NULL,
    `VALUE`     varchar(255) DEFAULT NULL,
    `EDIT_TIME` datetime     NOT NULL,
    PRIMARY KEY (`CODE`)
);

CREATE TABLE IF NOT EXISTS `vkapi_market_export_hash`
(
    `ID`        int(11)     NOT NULL AUTO_INCREMENT,
    `CODE`      varchar(20) NOT NULL,
    `HASH`      varchar(32) NOT NULL,
    `GROUP_ID`  int(11)     NOT NULL,
    `EXPORT_ID` int(11)     NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `ix_code` (`EXPORT_ID`, `GROUP_ID`, `CODE`)
);


CREATE TABLE IF NOT EXISTS `vkapi_market_album_item`
(
    `ID`      INT(11)      NOT NULL AUTO_INCREMENT,
    `NAME`    VARCHAR(255) NOT NULL,
    `VK_NAME` VARCHAR(255) NOT NULL,
    `PICTURE` int(11) DEFAULT NULL,
    `PARAMS`  LONGTEXT     NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE IF NOT EXISTS `vkapi_market_album_export_item`
(
    `ID`       int(11)     NOT NULL AUTO_INCREMENT,
    `GROUP_ID` int(11)     NOT NULL,
    `ALBUM_ID` int(11)     NOT NULL,
    `VK_ID`    int(11) DEFAULT NULL,
    `HASH`     varchar(32) NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `ix_export_album` (`GROUP_ID`, `ALBUM_ID`)
);

CREATE TABLE IF NOT EXISTS `vkapi_market_good_reference_album`
(
    `ID`         int(11) unsigned NOT NULL AUTO_INCREMENT,
    `ALBUM_ID`   int(11) unsigned NOT NULL,
    `PRODUCT_ID` int(11) unsigned DEFAULT NULL,
    `OFFER_ID`   int(11) unsigned DEFAULT NULL,
    PRIMARY KEY (`ID`),
    KEY `ix_album` (`ALBUM_ID`),
    KEY `ix_product_album` (`PRODUCT_ID`, `ALBUM_ID`),
    KEY `ix_offer` (`OFFER_ID`)
);

CREATE TABLE IF NOT EXISTS `vkapi_market_good_reference_export`
(
    `ID`         int(11) unsigned NOT NULL AUTO_INCREMENT,
    `EXPORT_ID`  int(11) unsigned NOT NULL,
    `PRODUCT_ID` int(11) unsigned    DEFAULT NULL,
    `OFFER_ID`   int(11)             DEFAULT NULL,
    `FLAG`       TINYINT(1) unsigned DEFAULT NULL,
    PRIMARY KEY (`ID`),
    KEY `ix_export_flag` (`EXPORT_ID`, `FLAG`),
    KEY `ix_product_export` (`PRODUCT_ID`, `EXPORT_ID`),
    KEY `ix_offer_export` (`OFFER_ID`, `EXPORT_ID`)
);


CREATE TABLE IF NOT EXISTS `vkapi_market_good_export`
(
    `ID`         int(11) unsigned NOT NULL AUTO_INCREMENT,
    `GROUP_ID`   int(11) unsigned NOT NULL,
    `PRODUCT_ID` int(11) unsigned DEFAULT NULL,
    `OFFER_ID`   int(11) unsigned DEFAULT NULL,
    `VK_ID`      int(11) unsigned DEFAULT NULL,
    `HASH`       varchar(32)      NOT NULL,
    PRIMARY KEY (`ID`),
    KEY `ix_group` (`GROUP_ID`),
    KEY `ix_product_group` (`PRODUCT_ID`, `GROUP_ID`),
    KEY `ix_offer_group` (`OFFER_ID`, `GROUP_ID`)
);



CREATE TABLE IF NOT EXISTS `vkapi_market_property`
(
    `ID`             int(11) unsigned NOT NULL AUTO_INCREMENT,
    `GROUP_ID`       int(11) unsigned NOT NULL,
    `PROPERTY_ID`    int(11) unsigned DEFAULT NULL,
    `VK_PROPERTY_ID` int(11) unsigned DEFAULT NULL,
    PRIMARY KEY (`ID`),
    KEY `ix_gid_pid` (`GROUP_ID`, `PROPERTY_ID`)
);

CREATE TABLE IF NOT EXISTS `vkapi_market_property_variant`
(
    `ID`            int(11) unsigned NOT NULL AUTO_INCREMENT,
    `GROUP_ID`      int(11) unsigned NOT NULL,
    `PROPERTY_ID`   int(11) unsigned DEFAULT NULL,
    `ENUM_ID`       int(11) unsigned DEFAULT NULL,
    `VK_VARIANT_ID` int(11) unsigned DEFAULT NULL,
    PRIMARY KEY (`ID`),
    KEY `ix_gid_pid_eid` (`GROUP_ID`, `PROPERTY_ID`, `ENUM_ID`)
);

CREATE TABLE IF NOT EXISTS `vkapi_market_sale_order_sync`
(
    `ID`                 INT(11)    NOT NULL AUTO_INCREMENT,
    `ACTIVE`             TINYINT(1)   DEFAULT 0,
    `ACCOUNT_ID`         INT(11)    NOT NULL,
    `GROUP_ID`           INT(11)    NOT NULL,
    `GROUP_NAME`         VARCHAR(255) DEFAULT NULL,
    `EVENT_ENABLED`      TINYINT(1)   DEFAULT 1,
    `EVENT_SECRET`       VARCHAR(255) DEFAULT '',
    `EVENT_CODE`         VARCHAR(255) DEFAULT '',
    `GROUP_ACCESS_TOKEN` VARCHAR(255) DEFAULT '',
    `SITE_ID`            VARCHAR(2) NOT NULL,
    `PARAMS`             LONGTEXT   NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE IF NOT EXISTS `vkapi_market_sale_order_sync_ref`
(
    `ID`         INT(11) NOT NULL AUTO_INCREMENT,
    `ORDER_ID`   INT(11) NOT NULL,
    `VKORDER_ID` INT(11) NOT NULL,
    `VKUSER_ID`  INT(11) NOT NULL,
    `GROUP_ID`   INT(11) NOT NULL,
    `SYNC_ID`    INT(11) NOT NULL,
    PRIMARY KEY (`ID`),
    KEY `ix_oid` (`ORDER_ID`),
    KEY `ix_vkoid_vkuid` (`VKORDER_ID`, `VKUSER_ID`)
);

CREATE TABLE IF NOT EXISTS `vkapi_market_export_limit_good`
(
    `ID`        INT(11)  NOT NULL AUTO_INCREMENT,
    `EXPORT_ID` INT(11)  NOT NULL,
    `GROUP_ID`  INT(11)  NOT NULL,
    `VK_ID`     INT(11)  NOT NULL,
    `CREATED`   DATETIME NOT NULL,
    PRIMARY KEY (`ID`),
    KEY `ix_gid_eid` (`GROUP_ID`, `EXPORT_ID`)
);



CREATE TABLE IF NOT EXISTS `vkapi_market_export_history_good`
(
    `ID`             int(11) unsigned NOT NULL AUTO_INCREMENT,
    `GROUP_ID`       int(11) unsigned NOT NULL,
    `PRODUCT_ID`     int(11) unsigned DEFAULT NULL,
    `PRODUCT_XML_ID` varchar(70)      DEFAULT NULL,
    `PRODUCT_IBLOCK_ID`int(11) unsigned DEFAULT NULL,
    `OFFER_ID`       int(11) unsigned DEFAULT NULL,
    `OFFER_XML_ID`   varchar(70)      DEFAULT NULL,
    `OFFER_IBLOCK_ID`int(11) unsigned DEFAULT NULL,
    `SKU`            varchar(50)      DEFAULT NULL,
    `VK_ID`          int(11) unsigned DEFAULT NULL,
    `CREATED`        DATETIME         NOT NULL,
    PRIMARY KEY (`ID`),
    KEY `ix_gid_vkid` (`GROUP_ID`, `VK_ID`),
    KEY `ix_gid_sku` (`GROUP_ID`, `SKU`)
);