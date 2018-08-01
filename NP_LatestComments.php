<?php
/**
  * This plugin can be used to display the last few comments.

    Updates and documentation can be found here on the Nucleus Wiky:
    http://www.xiffy.nl/wakka/LatestComments



    History:
      v0.91 - Fixed XHTML warning
      v1.0  - Performance enhancement
      v1.1  - option to add extra <br /> between comment
            - change LatestComments(#,0) to LatestComments(#,actual) to
              display comment to actual/current blog.... the old code didn't
              work for some reason
            - added anchor to comment
      v1.2  - e-Musty (krank@krank.hu), 17/07/2004
              supportsFeature function was missing, however the code itself has 
              been already based on its support. This function has been added.
              Now compatible with Nucleus 3.
      v1.3  - improve comment tag naming
            - add "..." only when the comment is longer than comment display length (Thanks to JH)
      v1.4  - template formatting: %u (user), %c (comment), %t (time), %p (item)
      v1.5  - split %u into %u (user) and %l (link to comment)
      v1.5a - add %P
      v1.6  - call PreItem event to enable NP_Smiley support
            - batch all comments to display in one shot
	  v1.7  - mod by PiyoPiyoNaku (http://www.renege.net), 03-Feb-2007
			  option to change date format.
	  v1.71 - mod by PiyoPiyoNaku, 04-Feb-2007
			- fix my typo on the history
			- compatible with NP_Alias
	  v1.8	- mod by PiyoPiyoNaku, 02-Mar-2007
			- adds third skinvar parameter <%LatestComments(,,member)%> to have output similar to NP_MemberComments
	  v1.81 - mod by PiyoPiyoNaku, 09-Mar-2007
			- code cleaning regarding compatibility with NP_Alias [Must use the new NP_Alias v1.3 to make the compatibility works]
	  v1.82 - mod by PiyoPiyoNaku, 12-Mar-2007
			- language support for Japanese-utf8
	  v1.83 - mod by PiyoPiyoNaku <http://mixi.jp/show_friend.pl?id=16761236>, 16-Mar-2007
			- maximum number of characters for each name
	  v1.84 - mod by Rork
			- maximum number of characters for each title
			- support for complete and shortened title/name/comment
			- optional appended dots for shortened name
			- no comments message
			- support for commentid (%i)
          v1.85 - mod by Rork
                        - support for NP_BBCode
			- support for different odd / even comments
			- check if comment doesn't break with a word allready
  */

class NP_LatestComments extends NucleusPlugin {

	function getEventList() { return array(); }
	function getName() { return 'Latest Comments'; }
	function getAuthor()  { return 'anand | moraes | admun | e-Musty | PiyoPiyoNaku | Rork'; }
	function getURL()  { return 'http://www.renege.net/'; }
	function getVersion() { return '1.85'; }
	function getDescription() {
		return _LCOM_DESC;
	}
	
	//<mod by PiyoPiyoNaku>
	function init() {
		$language = str_replace( array('\\','/'), '', getLanguageName());
		if ($language == "japanese-utf8")
		{
			define('_LCOM_DESC',			'最新のコメントを表示するプラグイン。 スキンへの記述： &lt;%LatestComments%&gt;');
			define('_LCOM_OPTHEAD',			'コメントの一覧のヘッダ。デフォルトは 「&lt;ul&gt;」');
			define('_LCOM_OPTFORMAT',		'コメント一覧の本体。デフォルトは 「&lt;li&gt;&lt;a href="%l" title="Posts to: %p"&gt;%u&lt;/a&gt; says %c&lt;/li&gt;」');
			define('_LCOM_OPTFORMATEVEN',	'Comment formatting (even comments). Defaults to comment format odd/all');
			define('_LCOM_OPTFOOT',			'コメントの一覧のフッタ。デフォルトは 「&lt;/ul&gt;」');
			define('_LCOM_DATEFORMAT',		'日付の形式。デフォルトは 「Y-m-d H:i:s」');
			define('_LCOM_OPTNOCOMMENTS',	'Message when there are no comments. Default: No comments yet');
			define('_LCOM_OPT1',			'ディスプレイ名はメンバー短縮名？デフォルトは 「はい」');
			define('_LCOM_OPT2',			'1コメント中に表示するキャラクターの数。デフォルトは 「85」');
			define('_LCOM_OPT3',			'ワードの終わりにコメントはブレイクするですか？デフォルトは 「はい」');
			define('_LCOM_OPT4',			'ディスプレイ名のキャラクターの数。デフォルトは 「15」');
			define('_LCOM_OPT5',			'Max characters of display title. Default is 22');
			define('_LCOM_OPT6',			'Append ... to shortened name. Default = yes');
			define('_LCOM_USEMARKUP',		'Use markup in comments (BBCode) (Default = no)');
			define('_LCOM_REPLACEIMG',		'Replace bbcode images by (leave empty te keep images)');
			define('_LCOM_REPLACEYOUTUBE',	'Replace bbcode youtube movies by (leave empty te keep movie)');
		}
		else
		{
			define('_LCOM_DESC',			'This plugin can be used to display the last few comments. Skinvar: &lt;%LatestComments%&gt;');
			define('_LCOM_OPTHEAD',			'Header formatting. Default is &lt;ul&gt;');
			define('_LCOM_OPTFORMAT',		'Comment formatting (odd / all comments). Default is <li><a href="%l" title="Posts to: %P">%U</a> says %c</li>');
			define('_LCOM_OPTFORMATEVEN',	'Comment formatting (even comments). Defaults to comment format odd/all');
			define('_LCOM_OPTFOOT',			'Footer formatting. Default is &lt;/ul&gt;');
			define('_LCOM_DATEFORMAT',		'Date format. Default is Y-m-d H:i:s');
			define('_LCOM_OPTNOCOMMENTS',	'Message when there are no comments. Default: No comments yet');
			define('_LCOM_OPT1',			'Display name is short member name? Default is Yes');
			define('_LCOM_OPT2',			'Max characters in each comment. Default is 85');
			define('_LCOM_OPT3',			'Break comment at the end of the word? Default is Yes');
			define('_LCOM_OPT4',			'Max characters of display name. Default is 15');
			define('_LCOM_OPT5',			'Max characters of display title. Default is 22');
			define('_LCOM_OPT6',			'Append ... to shortened name. Default = yes');	
			define('_LCOM_USEMARKUP',		'Use markup in comments (BBCode) (Default = no)');
			define('_LCOM_REPLACEIMG',		'Replace bbcode images by (leave empty te keep images)');
			define('_LCOM_REPLACEYOUTUBE',	'Replace bbcode youtube movies by (leave empty te keep movie)');
		}
	}
	//</mod by PiyoPiyoNaku>

	function install() {
		$this->createOption('option1',_LCOM_OPT1,'yesno','yes');
		$this->createOption('option2',_LCOM_OPT2,'text','85');
		$this->createOption('option3',_LCOM_OPT3,'yesno','yes');
		//<mod by PiyoPiyoNaku>
		$this->createOption('option4',_LCOM_OPT4,'text','15');
		//<mod by Rork>
		$this->createOption('option5',_LCOM_OPT5,'text','22');
		$this->createOption('option6',_LCOM_OPT6,'yesno','yes');

     		$this->createOption('dateformat',_LCOM_DATEFORMAT,'text','Y-m-d H:i:s');
		//</mod by PiyoPiyoNaku>
		$this->createOption('header',_LCOM_OPTHEAD,'textarea','<ul>');
		$this->createOption('comment',_LCOM_OPTFORMAT,'textarea','<li><a href="%l" title="Posts to: %P">%U</a> says %c</li>');
		$this->createOption('commenteven', _LCOM_OPTFORMATEVEN,'textarea','');
		$this->createOption('footer',_LCOM_OPTFOOT,'textarea','</ul>');
		$this->createOption('nocomments', _LCOM_OPTNOCOMMENTS,'textarea','No comments yet');
		//<mod by Rork: add NP_BBCode support>
                $this->createOption('usemarkup',_LCOM_USEMARKUP,'yesno','no');
		$this->createOption('bbc_replaceimg', _LCOM_REPLACEIMG,'textarea','');
		$this->createOption('bbc_replaceyoutube', _LCOM_REPLACEYOUTUBE,'textarea','');
		//</mod by rork>
	}

	// Make it compatible w/ Nucleus 3
	function supportsFeature($feature) {
		switch($feature) {
			case 'SqlTablePrefix':
				return 1;
			default:
				return 0;
		}
	}

	// skinvar plugin can have a blogname as second parameter
	function doSkinVar($skinType) {
		global $manager, $blog, $CONF, $memberid;
		$params = func_get_args();
		$option1 = $this->getOption('option1');
		$option2 = $this->getOption('option2');
		$option3 = $this->getOption('option3');
		$comm_templ_odd = $this->getOption('comment');
		$comm_templ_even = $this->getOption('commenteven');

                if (empty($comm_templ_even)) {
			$comm_templ_even = $comm_templ_odd;
                }

		$numberOfComments   = 5; // default number of comments

		if ($option2) { 
			$numberOfCharacters = $option2; 
		} else { 
			$numberOfCharacters = 85; 
		}
		
		//<mod by PiyoPiyoNaku>
		$option4 = $this->getOption('option4');
		if ($option4) { 
			$numberOfName = $option4; 
		} else { 
			$numberOfName = 15;
		}
		//</mod by PiyoPiyoNaku>

		//<mod by Rork>
		$option5 = $this->getOption('option5'); // max title length
		if ($option5) {
			$numberOfTitle = $option5;
		} else {
			$numberOfTitle = 22;
		}

		$option6 = $this->getOption('option6'); // append dots to shortened name
		if ($option6) {
			$appendDotsToName = $option6;
		} else {
			$appendDotsToName = "yes";
		}

                $usemarkup = $this->getOption('usemarkup');
                if (!isset($usemarkup)) {
			$usemarkup = "no";
                }

		$bbc_replaceimg = $this->getOption('bbc_replaceimg');
		$bbc_replaceyoutube = $this->getOption('bbc_replaceyoutube');
		//</mod by Rork>


		// how many comments will be shown?
		if ($params[1]) {
			$numberOfComments = $params[1]; 
		}

		// show comments from all blogs
		if (!$params[2]) { 
			$blogid = "";
		}
		// show comments from the actual blog
		else if ($params[2] == "actual") { 
			$blogid = " WHERE cblog=".$blog->getID(); 
		}
		// show comments from the default blog
		else if ($params[2] == "default") {
			$blogid = " WHERE cblog=".$CONF['DefaultBlog'];  
		}
		// show comments from the selected blog id
		else { 
			$blogid = " WHERE cblog=".($params[2]); 
		}
		
		//<mod by PiyoPiyoNaku>
		if(!$params[3]) {
			$memberonly = '';
		}
		else if ($params[3] == "member") {
			if (!$blogid) {
				$memberonly = ' WHERE cmember='. $memberid;
			}
			else {
				$memberonly = ' AND cmember='. $memberid;
			}
		}

		$query = "SELECT cuser, cbody, citem, cmember, ctime, cnumber FROM ".sql_table('comment')." ".$blogid.$memberonly." ORDER by ctime DESC LIMIT 0,".$numberOfComments;
		//</mod by PiyoPiyoNaku>

		$comments = mysql_query($query);

		echo($this->getOption('header'));
		$out = "";

		//<mod by Rork>
		if (mysql_num_rows($comments) == 0) {
			echo $this->getOption('nocomments');
		}
		//</mod by Rork>
		$i = 1;
		while($row = mysql_fetch_object($comments)) {
			$text  = $row->cbody;
                        // mod by rork, BBCode support
			$use_bbcode = checkPlugin("NP_BBCode");
			$bbcode_regexp = '/\[\/?(b|u|i|s|size[^\]]*|color[^\]]*|quote[^\]]*|code|url[^\]]*|email[^\]]*|img|youtube)\]/';

			if ($use_bbcode) {
				// replace links
				$text = preg_replace('/\<a href=\"(.*)\" rel=\"nofollow\">(.*)\<\/a\>/i','\1',$text);

				// no markup? remove bbcode
				if ($usemarkup == "no") {
					// 
					$text = preg_replace($bbcode_regexp, '', $text);
				}
				else {
					if (!empty($bbc_replaceimg)) {
					      $text = preg_replace('/\[img\](.*?)\[\/img\]/Ui', $bbc_replaceimg, $text);
					}
					if (!empty($bbc_replaceyoutube)) {
					      $text = preg_replace('/\[youtube\](.*?)\[\/youtube\]/Ui', $bbc_replaceyoutube, $text);
					}
				}
				// NP_Code handles the bb tags magically!
			}
			// End mod
			$text  = strip_tags($text);
			$ctime =  $row->ctime;


			//only process this loop, if the sting is longer
			//than $numberOfCharacters
			$text_nomarkup = $text;
			if ($use_bbcode) {
					/* [b][/b] [i][/i] [u][/u] */
					/* [s][/s] */
					/* [color=][/color] [size=][/size] */
					/* [quote][/quote] [quote name=][/quote]*/
					/* [code][/code] */
					/* [url][/url] [url=][/url]*/
					/* [email][/email] [email=][/email]*/
					/* [img][/img] */
					/* [youtube][/youtube] */
				// $text_nomarkup = preg_replace('/\[youtube\](.*)\[\/youtube\]/Ui', '&#91;YouTube&#93; ', $text);
				$text_nomarkup = preg_replace($bbcode_regexp, '', $text);
			}

		// 	echo "Raw length = " . strlen($text) . "\n";
		// 	echo "True length = " . strlen($text_nomarkup) . "\n";
		// 	echo "Max length = " . $numberOfCharacters . "\n";

			if ((strlen($text_nomarkup) > $numberOfCharacters) and $use_bbcode) {
				// think of some smart way of shortening the comment
				$real_pos = 0;
				$relative_pos = 0;
				while ($relative_pos < $numberOfCharacters) {
				      // get te position of the next tag (using next < instead of [)
				      $result = preg_match($bbcode_regexp, $text, $matches, PREG_OFFSET_CAPTURE, $real_pos);
				      $nextlt = (isset($matches[0][1]) ? $matches[0][1] : false);

				      if ($nextlt === false) {
					      break;
				      }
				      if ($nextlt > $numberOfCharacters + $real_pos - $relative_pos) {
					      break;
				      }
				      else {
					      $relative_pos += $nextlt - $real_pos;
					      $real_pos = strpos($text, "]", $real_pos+1)+1;
		// 			      echo "Found [ at " . $nextlt . ", skip to " . $real_pos . "\n";
		// 			      echo "Real pos = " . $real_pos . ", relative position = " . $relative_pos . "\n";
				      }
				}
				$break_pos = $real_pos - ($relative_pos - $numberOfCharacters);
		// 		echo "Break at " . $break_pos . "\n";
				$ctext = substr($text, 0, $break_pos);

				// break comments by word?
				if (($option3 == "yes") and (strpos($text_nomarkup," ", $numberOfCharacters) != $numberOfCharacters)) {
					$ctext = substr($text, 0, strrpos($ctext," "));
				}
			      
				// check if we didn't break code
				if ($use_bbcode) {
					/* [b][/b] [i][/i] [u][/u] */
					/* [s][/s] */
					/* [color=][/color] [size=][/size] */
					/* [quote][/quote] [quote name=][/quote]*/
					/* [code][/code] */
					/* [url][/url] [url=][/url]*/
					/* [email][/email] [email=][/email]*/
					/* [img][/img] */
					/* [youtube][/youtube] */
					$bbcode_regexp_array["/\[b\]/"] = "b";
					$bbcode_regexp_array["/\[i\]/"] = "i";
					$bbcode_regexp_array["/\[u\]/"] = "u";
					$bbcode_regexp_array["/\[s\]/"] = "s";
					$bbcode_regexp_array["/\[color[^]]*\]/"] = "color";
					$bbcode_regexp_array["/\[size[^]]*\]/"] = "size";
					$bbcode_regexp_array["/\[quote[^]]*\]/"] = "quote";
					$bbcode_regexp_array["/\[url[^]]*\]/"] = "url";
					$bbcode_regexp_array["/\[email[^]]*\]/"] = "email";
					$bbcode_regexp_array["/\[img\]/"] = "img";
					$bbcode_regexp_array["/\[youtube\]/"] = "youtube";

					foreach ($bbcode_regexp_array as $regexp => $bbtag) {
					      $num_opening_tags = preg_match_all($regexp,$ctext, $fubar);
					      $num_closing_tags = preg_match_all("/\[\/".$bbtag."\]/", $ctext, $fubar);

					      if ($num_closing_tags < $num_opening_tags) {
						      for ($i = $num_opening_tags - $num_closing_tags;$i > 0; $i--) {
							      $ctext .= "[/".$bbtag."]";
						      }
					      }
					}
				}
			}
			else if ( strlen($text) > $numberOfCharacters ) {
				//first cut off the characters
				//behind $numberOfCharacters
				$ctext = substr($text,0,$numberOfCharacters);

				// break comments by word? !MOD Rork: if not at wordend allready!
				if (($option3 == "yes") and (strpos($text, " ", $numberOfCharacters) != $numberOfCharacters)) {	
					//now find the last " " within the string and
					//extract the part before that " "	   
					$ctext = substr($ctext,0,strrpos($ctext," "));
				}
			}
			else {
				// else use the string as it is
				$ctext = $text;
			}


			if (!$row->cmember) {
				$myname = $row->cuser;
			} else {
				$mem = new MEMBER;
				$mem->readFromID(intval($row->cmember));
				// show short member names
				if ($option1 == "yes") {
					//<mod by PiyoPiyoNaku>
					$myname = $mem->getDisplayName();
					$pluginName = 'NP_Alias';
					if ($manager->pluginInstalled($pluginName))
					{
						$pluginObject =& $manager->getPlugin($pluginName);
						if ($pluginObject) {
							$myname = $pluginObject->getAliasfromMemberName($myname);
						}
					}
					//</mod by PiyoPiyoNaku>
				}
				// show real member names
				else { 
					$myname = $mem->getRealName(); 
				}
			}
			
			//<mod by PiyoPiyoNaku>
			if ( strlen($myname) > $numberOfName ) {
				$shortDisplayedName = substr($myname,0,$numberOfName);
				if ($appendDotsToName == "yes") {
					$shortDisplayedName .= "...";
				}
				$displayedName = $myname;
			}
			else {
				$displayedName = $myname;
				$shortDisplayedName = $myname;
			}
			//</mod by PiyoPiyoNaku>

			$itemlink = createItemLink($row->citem, '');

			if ( strlen($text) > $numberOfCharacters ) {
				$ctext .= "...";
			}

			// use  TEMPLATE:fill function?

                        // different template for odd / even comments
                        $comm_templ = $comm_templ_odd;
			if ($i % 2 == 0) {
                           $comm_templ = $comm_templ_even;
                        }

			// echo "Use template: " . $comm_templ . "<br><br>\n";

			$comm_out = str_replace("%U", $displayedName, $comm_templ);
			$comm_out = str_replace("%u", $shortDisplayedName, $comm_out);
			$comm_out = str_replace("%l", $IndexURL.$itemlink."#".$row->cnumber, $comm_out);
			$comm_out = str_replace("%L", $IndexURL.$itemlink, $comm_out);
			$comm_out = str_replace("%C", $text, $comm_out);
			$comm_out = str_replace("%c", $ctext, $comm_out);
			$comm_out = str_replace("%i", $row->cnumber, $comm_out);

			//<mod by PiyoPiyoNaku>
			$comm_out = str_replace("%t", date($this->getOption('dateformat'),strtotime($ctime)), $comm_out);
			//</mod by PiyoPiyoNaku>
			//<mod by Rork>
			if (strpos($comm_out, "%p") or strpos($comm_out, "%P")) {
				$citem = $manager->getItem($row->citem, 0, 0);

				$comm_out = str_replace("%P", $citem['title'], $comm_out);
				if (strlen($citem['title']) > $numberOfTitle) {
					 $citem['title'] = substr($citem['title'], 0, ($numberOfTitle - 3)) . "...";
				}
				$comm_out = str_replace("%p", $citem['title'], $comm_out);
			}

			// echo "add " . $comm_out . "<br><br>\n";

			$out .= $comm_out;
			$i++;
			//</mod by Rork>
		}

		// Call PreComment event to trigger other plugins to process the output before we display it
		$comment['body'] = $out;
		$manager->notify('PreComment', array('comment' => &$comment));
		echo($comment['body']);

		echo($this->getOption('footer'));

	}
}
?>