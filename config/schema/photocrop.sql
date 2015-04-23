--
-- Table structure for table `photocrops`
--
CREATE TABLE IF NOT EXISTS `photocrops` (
`id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  -- `user_id` int(10) unsigned DEFAULT NULL,
  `mime` varchar(25) DEFAULT NULL,
  `width` smallint(6) DEFAULT NULL,
  `height` smallint(6) DEFAULT NULL,
  `bits` mediumint(9) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Indexes for table `photocrops`
--
-- ALTER TABLE `photocrops`
-- ADD PRIMARY KEY (`id`), ADD KEY `fk_photocrops_users_idx` (`user_id`);

--
-- AUTO_INCREMENT for table `photocrops`
--
ALTER TABLE `photocrops`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
