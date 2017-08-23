<?php
function genQunInfo() {
	$fd = fopen("gid-gname-cuid.txt", "wb");
	$fi = fopen("now_club_member_emails.txt", "rb");
	$uidGids = file("UID-GID.txt");
	foreach($uidGids as $uidGid) {
		list($uid, $gid) = explode("\t", trim($uidGid));
		while($line = fgets($fi)) {
			$tmpArr = explode("\t", trim($line));
			if (count($tmpArr) < 3) {
				continue;
			}
			list($email, $uid2, $gname) = $tmpArr;
			if ($uid == $uid2) {
				echo "$gid\t$gname\t$uid\n";
				fwrite($fd, $gid . "\t" . $gname . "\t" . $uid . "\n");
				break;
			}
		}
		rewind($fi);
	}
	fclose($fd);
	fclose($fi);
}

genQunInfo();
