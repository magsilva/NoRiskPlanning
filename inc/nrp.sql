-- Server version	4.0.15-log

--
-- Table structure for table `accounts`
--

CREATE TABLE accounts (
  account_id varchar(32) NOT NULL default '',
  role varchar(20) default NULL,
  name tinytext NOT NULL,
  commentaries text,
  PRIMARY KEY  (account_id)
) TYPE=MyISAM;

INSERT INTO accounts VALUES ('admin','admin','System Administrator',NULL);

--
-- Table structure for table `appointments`
--

CREATE TABLE appointments (
  app_id bigint(20) NOT NULL auto_increment,
  account_id varchar(32) default NULL,
  description varchar(100) NOT NULL default '',
  type int(11) NOT NULL default '0',
  date date NOT NULL default '0000-00-00',
  beg_time smallint(6) NOT NULL default '0',
  end_time smallint(6) NOT NULL default '0',
  url varchar(100) default NULL,
  owner varchar(32) NOT NULL default '',
  group_id int(11) default NULL,
  is_group_app int(11) NOT NULL default '0',
  authorized int(11) default NULL,
  room_id varchar(32) default NULL,
  PRIMARY KEY  (app_id)
) TYPE=MyISAM;

--
-- Table structure for table `categories`
--

CREATE TABLE categories (
  cat_id smallint(6) NOT NULL auto_increment,
  name varchar(30) NOT NULL default '',
  description varchar(100) default NULL,
  PRIMARY KEY  (cat_id)
) TYPE=MyISAM;

--
-- Table structure for table `courses`
--

CREATE TABLE courses (
  account_id varchar(32) NOT NULL default '',
  code varchar(16) default NULL,
  class varchar(16) default NULL,
  year int(11) NOT NULL default '0',
  semester int(11) NOT NULL default '0',
  lecturer varchar(32) NOT NULL default '',
  PRIMARY KEY  (account_id)
) TYPE=MyISAM;

--
-- Table structure for table `departments`
--

CREATE TABLE departments (
  dep_id int(11) NOT NULL auto_increment,
  unit_id int(11) NOT NULL default '0',
  name varchar(50) NOT NULL default '',
  acronym varchar(10) default NULL,
  description varchar(100) NOT NULL default '',
  PRIMARY KEY  (dep_id)
) TYPE=MyISAM;

--
-- Table structure for table `group_docs`
--

CREATE TABLE group_docs (
  doc_id bigint(20) NOT NULL auto_increment,
  account_id varchar(32) NOT NULL default '',
  group_id int(11) NOT NULL default '0',
  size int(11) NOT NULL default '0',
  name text NOT NULL,
  description tinytext NOT NULL,
  PRIMARY KEY  (doc_id)
) TYPE=MyISAM;

--
-- Table structure for table `group_members`
--

CREATE TABLE group_members (
  group_id int(11) NOT NULL default '0',
  account_id varchar(32) NOT NULL default '',
  membership char(1) NOT NULL default '',
  confirm_code varchar(32) default NULL,
  PRIMARY KEY  (group_id,account_id)
) TYPE=MyISAM;

--
-- Table structure for table `group_notices`
--

CREATE TABLE group_notices (
  group_id int(11) NOT NULL default '0',
  account_id varchar(32) NOT NULL default '',
  notice_id int(11) NOT NULL auto_increment,
  date date NOT NULL default '0000-00-00',
  time time NOT NULL default '00:00:00',
  description tinytext NOT NULL,
  PRIMARY KEY  (notice_id)
) TYPE=MyISAM;

--
-- Table structure for table `groups`
--

CREATE TABLE groups (
  group_id int(11) NOT NULL auto_increment,
  category varchar(40) default '0',
  name tinytext NOT NULL,
  acronym varchar(15) default NULL,
  description tinytext,
  PRIMARY KEY  (group_id)
) TYPE=MyISAM;

--
-- Table structure for table `people`
--

CREATE TABLE people (
  account_id varchar(32) NOT NULL default '',
  email varchar(80) NOT NULL default '',
  dep_id int(11) default NULL,
  url varchar(100) default NULL,
  password varchar(40) NOT NULL default '',
  public_types varchar(40) default NULL,
  category varchar(32) default NULL,
  PRIMARY KEY  (account_id)
) TYPE=MyISAM;

INSERT INTO people VALUES ('admin','admin@admin',0,NULL,'2fece8e20defecf3d05cf1e29bea9e75',NULL,NULL);

--
-- Table structure for table `relationships`
--

CREATE TABLE relationships (
  rel_id bigint(20) NOT NULL auto_increment,
  master_id varchar(32) default NULL,
  master_group int(11) default NULL,
  master_category varchar(32) default NULL,
  slave_id varchar(32) NOT NULL default '',
  rel_type varchar(30) NOT NULL default '',
  PRIMARY KEY  (rel_id)
) TYPE=MyISAM;

--
-- Table structure for table `rooms`
--

CREATE TABLE rooms (
  account_id varchar(32) NOT NULL default '',
  code varchar(16) default NULL,
  capacity int(11) default NULL,
  location text,
  type varchar(30) default NULL,
  PRIMARY KEY  (account_id)
) TYPE=MyISAM;

--
-- Table structure for table `sessions`
--

CREATE TABLE sessions (
  session_id bigint(20) NOT NULL auto_increment,
  master_session_id bigint(20) NOT NULL default '0',
  account_id varchar(32) NOT NULL default '',
  begin datetime NOT NULL default '0000-00-00 00:00:00',
  end datetime NOT NULL default '0000-00-00 00:00:00',
  active smallint(6) NOT NULL default '0',
  end_cause varchar(32) default NULL,
  ip_address varchar(15) NOT NULL default '',
  exibition varchar(64) NOT NULL default '',
  PRIMARY KEY  (session_id)
) TYPE=MyISAM;

--
-- Table structure for table `units`
--

CREATE TABLE units (
  unit_id smallint(6) NOT NULL auto_increment,
  name varchar(50) NOT NULL default '',
  acronym varchar(15) NOT NULL default '',
  description varchar(100) default NULL,
  PRIMARY KEY  (unit_id)
) TYPE=MyISAM;

--
-- Table structure for table `weekly_appointments`
--

CREATE TABLE weekly_appointments (
  app_id bigint(20) NOT NULL auto_increment,
  account_id varchar(32) default NULL,
  description varchar(100) NOT NULL default '',
  type int(11) NOT NULL default '0',
  day smallint(6) NOT NULL default '0',
  beg_time int(11) NOT NULL default '0',
  end_time int(11) NOT NULL default '0',
  url varchar(100) default NULL,
  owner varchar(32) NOT NULL default '',
  group_id int(11) default NULL,
  is_group_app int(11) NOT NULL default '0',
  authorized int(11) default NULL,
  room_id varchar(32) default NULL,
  PRIMARY KEY  (app_id),
  KEY account_id (account_id)
) TYPE=MyISAM;

