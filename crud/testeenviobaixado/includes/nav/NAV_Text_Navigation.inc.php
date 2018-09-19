<div class="KT_textnav clearfix">
  <ul>
		<li class="first">
			<a href="<?php 
				if ($GLOBALS['nav_pageNum'] > 0) {
					printf("%s?pageNum_".$GLOBALS['nav_rsName']."=%d&totalRows_".$GLOBALS['nav_rsName']."=%d%s", $GLOBALS['nav_currentPage'], 0, $GLOBALS['nav_totalRows'], $GLOBALS['nav_queryString']); 
				} else {
					echo "javascript: void(0);";
				}?>"><?php echo NAV_getResource("First"); ?></a>
		</li>
		<li class="prev">
				<a href="<?php
				if ($GLOBALS['nav_pageNum'] > 0) {
					printf("%s?pageNum_".$GLOBALS['nav_rsName']."=%d&totalRows_".$GLOBALS['nav_rsName']."=%d%s", $GLOBALS['nav_currentPage'], max(0, $GLOBALS['nav_pageNum'] - 1), $GLOBALS['nav_totalRows'], $GLOBALS['nav_queryString']);
				} else {
					echo "javascript: void(0);";
				}
				?>"><?php echo NAV_getResource("Previous"); ?></a>
		</li>
		<li class="next">
			<a href="<?php 
				if ($GLOBALS['nav_pageNum'] < $GLOBALS['nav_totalPages']) {
					printf("%s?pageNum_".$GLOBALS['nav_rsName']."=%d&totalRows_".$GLOBALS['nav_rsName']."=%d%s", $GLOBALS['nav_currentPage'], min($GLOBALS['nav_totalPages'], $GLOBALS['nav_pageNum'] + 1), $GLOBALS['nav_totalRows'], $GLOBALS['nav_queryString']); 
				} else {
					echo "javascript: void(0);";
				}?>"><?php echo NAV_getResource("Next"); ?></a>
		</li>
		<li class="last">
			<a href="<?php
				if ($GLOBALS['nav_pageNum'] < $GLOBALS['nav_totalPages']) {
					printf("%s?pageNum_".$GLOBALS['nav_rsName']."=%d&totalRows_".$GLOBALS['nav_rsName']."=%d%s", $GLOBALS['nav_currentPage'], $GLOBALS['nav_totalPages'], $GLOBALS['nav_totalRows'], $GLOBALS['nav_queryString']); 
				} else {
					echo "javascript: void(0);";
				}?>"><?php echo NAV_getResource("Last"); ?></a>
		</li>
  </ul>
</div>