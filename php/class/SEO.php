<?php //updated:2020-01-29 20:20:33 SEO- v.0.73 - Author:Nikos Drosakis - License: GPL License ?>

<?php 
/*
SEO CLASS 
USES
- CREATE XML FILES
- PROFILE HTML DYNAMICS (OR GLOBAL) METATAGS
- ADMINISTRATION PANEL

INSERNAL AND EXTERNAL USES
automatically 
-- updates Search Engine Ping Services  *A Ping is a mechanism use by weblogs in informing search engines and other web directories that it has been updated by new content or post.
-- pingpbacks 							* A pingback is a type of comment that's created when you link to another blog post 
-- administrates Trackbacks – must be created manually, and send an excerpt of the content.
Take Care !!! Too Much Pinging Can Cost Google Rankings!

PART 1 - xml_creator use at DASHBOARD > SEO > SITEMAPS AND FEED
sitemap.xml, feed.xml, atom.xml, rss.xml  automated XML generator 

PART 2 - METATAGS - PAGE_METATAGS - use at DASHBOARD > SEO > METATAGS

PART 3 - SETTINGS ADMIN PAGE OF CONNECTION TO SOCIAL MEDIA AND FEED, API, FACEBOOK

METHOD 4 - ping xml to engines 
Daily submit to search engines 
https://www.bing.com/webmaster/ping.aspx?siteMap=https://www.example.com/sitemap.xml
https://www.google.com/webmasters/sitemaps/ping?sitemap=https://www.example.com/sitemap.xml
yahoo needs manually

UPDATE: feed the large feed sources 
UPDATE: GOOGLE FEED

Developed by Nikos Drosakis (c)2014-2016
3 types of seo data exist
- post
- page
- user

*/


class SEO extends Gaia {
	private 	$xml;
	private 	$refresh_time = 5;	
	private 	$priority;
	public 		$urli;
	public 		$xmls;
	public 		$rData;
	public 		$seoPages;
	public 		$langs;
	public 		$postlist;
	public 		$userlist;
	public 		$seo;
	public 		$root;

	public function __construct(){
	$my=new My;
	$this->postlist=$my->post();
	$this->userlist=$my->user();
//		xecho($_COOKIE);
	//	xecho($this->is('title'));
	
	//	$this->postlist= $this->fa("SELECT * FROM post WHERE status=2 ORDER BY seopriority,modified DESC");
	//	$this->userlist= $this->fa("SELECT * FROM user ORDER BY seopriority,modified DESC");
		
	}
		

/*	GLOBAL_METATAGS METHOD consist of the global metatags

	Authorship Markup – As I predicted at SearchLove NYC in November 2011, and has been verified by Eric Schmidt, Google is going to rank content that is connected to authors that they deem to be reliable sources over content that is not. A cool visible incentive is that you get name and your pretty picture in the SERPs if you’re an author.

	Rel-Author– This is a meta tag that can be implemented that specifies who the author of a piece of content is and uses Google+ to identify them. Initially Google rolled this out as just a tag that you place in the <head> of the code, but ultimately they would realize it’s not realistic that authors will have that type of control over the page and expanded to a more modular form.Format:For the version that goes in the <head> tag, you use the following:
	<link rel="author" href="https://plus.google.com/[YOUR PERSONAL G+ PROFILE HERE]“/>
	For the more modular version you would emulate XFN’s rel-me and place the link directly on the page. I’ve got to admit this is a great link building strategy.

	<a href="[profile_url]?rel=author">Google</a>

	If you use this method you will have to take the second step in verification by linking to your content from your Google+ profile. For more information see Google’s explanation.

	Rel-Me – Rel-me is just the XFNversion of rel-author. You simply place the meta tag on a link back to your Google+ profile.Format:
	<a href="https://plus.google.com/[YOUR PERSONAL G+ PROFILE NUMBER]" rel="me">Me on Google+</a>

	Rel-Publisher –Rel-publisher is for business entities to claim ownership of their content. This can be used in context with rel-author or in place of it, but you should be pointing to a business profile on Google+ rather than an individuals.Format:<link rel="publisher" href="https://plus.google.com/[YOUR BUSINESS G+ PROFILE HERE]“/>

	fb:admins – This metatag is critical for getting access to the wealth of data made available via Facebook Insights. You simply have to specify the Facebook User IDs in the metadata of those users you want to have access. For more information on Facebook Insights see the documentation.Format:
	<meta property="fb:admins" content="USER_ID"/> 
	
/*
	PAGE_METATAGS consist of the metatags per page
	<title>Example Books - high-quality used books for children</title>
	Page Title –
	Long regarded as the most important on-page factor, the title tag has recently taken a lot of scrutiny. A recent post has determined that page titles aren’t limited to 70 characters, but rather pixel-width. Also a little further back Cyrus Shepard tested titles longer than 70 characters to see what Google would do. In the wild I’ve seen extremely long titles are oftentimes chopped down or rewritten algorithmically to display the most relevant text to a query.
	Unless you want to measure the pixel-width of your titles and hope that Google shows the right thing, your best bet is to make page titles as keyword-relevant as possible and up to 70 characters. I honestly can’t think of a case where I’ve left something up to Google and they did a better job than I thought I could.

	Format:

	<title>Up to 70 Characters of Keyword-relevant text here</title>

	Meta Description – If your webpage were a commercial, this would be its slogan. In our upcoming search behavior study in cooperation with SurveyMonkey we’ve found that 43.2% of people click on a given result due to the meta description. Gone are the days of meta descriptions that listed keywords and just said the “Official site of…" and the main purpose of this text is to draw the user in, let them know what to expect if they click and convince them to do so with a strong call to action. The kicker is you get 155 characters to make it happen; think of it as like writing a tweet, but with 15 extra characters.
	Format:

	<meta name="description" content="155 characters of message matching text with a call to action goes here">

	<meta name="robots" content="..., ..." />
	<meta name="googlebot" content="..., ..." />	These meta tags can control the behavior of search engine crawling and indexing. The robots meta tag applies to all search engines, while the "googlebot" meta tag is specific to Google. The default values are "index, follow" (the same as "all") and do not need to be specified. We understand the following values (when specifying multiple values, separate them with a comma):
	noindex: prevents the page from being indexed
	nofollow: prevents the Googlebot from following links from this page
	nosnippet: prevents a snippet from being shown in the search results
	noodp: prevents the alternative description from the ODP/DMOZ from being used
	noarchive: prevents Google from showing the Cached link for a page.
	unavailable_after:[date]: lets you specify the exact time and date you want to stop crawling and indexing of this page
	noimageindex: lets you specify that you do not want your page to appear as the referring page for an image that appears in Google search results.
	none: is equivalent to noindex, nofollow.
	You can now also specify this information in the header of your pages using the "X-Robots-Tag" HTTP header directive. This is particularly useful if you wish to limit indexing of non-HTML files like graphics or other kinds of documents. More information about robots meta tags

	<meta name="google" content="nositelinkssearchbox" />	When users search for your site, Google Search results sometimes display a search box specific to your site, along with other direct links to your site. This meta tag tells Google not to show the sitelinks search box. Learn more about sitelinks search box.
	<meta name="google" content="notranslate" />	When we recognize that the contents of a page are not in the language that the user is likely to want to read, we often provide a link to a translation in the search results. In general, this gives you the chance to provide your unique and compelling content to a much larger group of users. However, there may be situations where this is not desired. This meta tag tells Google that you don't want us to provide a translation for this page.
	<meta name="google-site-verification" content="..." />	You can use this tag on the top-level page of your site to verify ownership for Webmaster Tools. Please note that while the values of the "name" and "content" attributes must match exactly what is provided to you (including upper and lower case), it doesn't matter if you change the tag from XHTML to HTML or if the format of the tag matches the format of your page. More information
	<meta http-equiv="Content-Type" content="...; charset=..." />
	<meta charset="..." >	This defines the page's content type and character set. Make sure that you surround the value of the content attribute with quotes - otherwise the charset attribute may be interpreted incorrectly. We recommend using Unicode/UTF-8 where possible. More information
	<meta http-equiv="refresh" content="...;url=..." />	This meta tag sends the user to a new URL after a certain amount of time, and is sometimes used as a simple form of redirection. However, it is not supported by all browsers and can be confusing to the user. The W3C recommends that this tag not be used. We recommend using a server-side 301 redirect instead.

	<meta charset="utf-8">
	<meta name="Description" CONTENT="Author: A.N. Author, Illustrator: P. Picture, Category: Books, Price:  £9.24, Length: 784 pages">
	<meta name="google-site-verification" content="+nxGUDJ4QpAZ5l9Bsjdi102tLVC21AIh5d1Nl23908vVuFHs34="/>

	<meta name="robots" content="noindex,nofollow">

	og:title –This is the title of the piece of content. You should use this as a headline that will appeal to the Facebook audience. It is completely ok to use a different title than the one on the actual site as long as the message is ultimately the same. You have 95 characters to work with.Format:
	<meta property="og:title" content="iAcquire’s awesome blog"/>
	og:type – This is the type of object your piece of content is. For your purposes it will usually be blog, website or article, but if you want to get fancy Facebook provides a complete list.Format:
	<meta property="og:type" content="article"/>
	og:image -This is the image that Facebook will show in the screenshot of the content. Be sure to specify a square image to ensure the best visibility in a user’s timeline. If you don’t specify an image at all you are left to the mercy of the user to pick which image represents your content based on what Facebook can scrape. That is typically not the way to ensure the best first impression.Format:
	<meta property="og:image" content="https://www.iacquire.com/some-thumbnail.jpg"/>

	og:url– This is simply the URL of the page (or edge). You should specify this especially if you have duplicate content issues to make sure the value of the edge in Facebook is consolidated into one URL.Format:
	<meta property="og:url" content="https://blog.iacquire.com"/>

	og:description -This is the description Facebook will show in the screenshot of the piece of content. Just like the standard meta description it should be catchy and contain a call to action, but in this case you have nearly twice the number of characters to work with. Make sure this too speaks to the Facebook audience. You have to 297 characters to make it happen.Format:
	<meta property="og:description" content="Stop hitting refresh on your ex-girlfriend’s Facebook page? You should check out the iAcquire blog and learn something instead"/>

*/
	
	/*
	CREATE_XML METHOD
		get_data
	Chooses the data that will expose to sitemaps


		From employer
		-----------------------
		user	experience
		photos / profile_photos
		promotion
		Ads για διαφημίσεις

		From employee
		-------------------
		user	experience
		photos / profile_photos
		employee_cv_text 		not closed cv
		cv
	Receive settings data: 
	-site_url
	-site_name
	-subtitle	
	-description
	-keywords
	-site_mail
	
	-- SITEMAP can improve search engine optimization of a site by making sure that all the pages can be found.
	-- RSS feeds enable publishers to syndicate data automatically. A standard XML file format ensures compatibility with many different machines/programs. RSS feeds also benefit users who want to receive timely updates from favourite websites or to aggregate data from many sites.		
	-- ATOM The Atom Syndication Format is an XML language used for web feeds, while the Atom Publishing Protocol (AtomPub or APP) is a simple HTTP-based protocol for creating and updating web resources.
	
	What data do we need to expose
	*/
	//---------------------SITEMAP---------------
	/*	SITEMAP TEMPLATE
		<?xml version="1.0" encoding="UTF-8"?>
		<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
		<url>
			<loc>https://www.example.com/?id=who</loc>
			<lastmod>2009-09-22</lastmod>
			<changefreq>monthly</changefreq>
			<priority>0.8</priority>
		  </url>
		  <url>
			<loc>https://www.example.com/?id=what</loc>
			<lastmod>2009-09-22</lastmod>
			<changefreq>monthly</changefreq>
			<priority>0.5</priority>
		  </url>
		</urlset>*/
	public function sitemap(){

			$sitemap = '<?xml version="1.0" encoding="utf-8"?>';
			$sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
			$sitemap .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
			$sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

		//root
			$sitemap .= "<url><loc>".SITE_URL."</loc>";
			$sitemap .= "<lastmod>".date('Y-m-d H:i:s')."</lastmod>";
			$sitemap .= "<changefreq>daily</changefreq>";
			$sitemap .= "<priority>1.0</priority></url>";

		//posts
		for ($i=0; $i <count($this->postlist); $i++){
			$sitemap .= "<url><loc>".$this->postlist[$i]["uri"]."</loc>";
			$sitemap .= "<lastmod>".date('Y-m-d H:i:s',$this->postlist[$i]["modified"])."</lastmod>";
			$sitemap .= "<changefreq>daily</changefreq>";
			$sitemap .= "<priority>".$this->postlist[$i]['seopriority']."</priority></url>";
			}
		//user
		for ($i=0; $i <count($this->userlist); $i++){
			$sitemap .= "<url><loc>".$this->userlist[$i]["uri"]."</loc>";
			$sitemap .= "<lastmod>".date('Y-m-d H:i:s',$this->userlist[$i]["modified"])."</lastmod>";
			$sitemap .= "<changefreq>daily</changefreq>";
			$sitemap .= "<priority>".$this->userlist[$i]['seopriority']."</priority></url>";
			}

				$sitemap .= '</urlset>';
			return $sitemap;
	}
	//----------------------------ATOM-------------
	/* ATOM TEMPLATE
    <?xml version="1.0" encoding="utf-8"?>
   <feed xmlns="http://www.w3.org/2005/Atom">
       <title>Example Feed</title>
       <subtitle>A subtitle.</subtitle>
       <link href="http://example.org/feed/" rel="self" />
       <link href="http://example.org/" />
       <id>urn:uuid:60a76c80-d399-11d9-b91C-0003939e0af6</id>
       <updated>2003-12-13T18:30:02Z</updated>
       <entry>
           <title>Atom-Powered Robots Run Amok</title>
           <link href="http://example.org/2003/12/13/atom03" />
           <link rel="alternate" type="text/html" href="http://example.org/2003/12/13/atom03.html"/>
           <link rel="edit" href="http://example.org/2003/12/13/atom03/edit"/>
           <id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
           <updated>2003-12-13T18:30:02Z</updated>
           <summary>Some text.</summary>
           <content type="xhtml">
               <div xmlns="http://www.w3.org/1999/xhtml">
                   <p>This is the entry content.</p>
               </div>
           </content>
           <author>
               <name>John Doe</name>
               <email>johndoe@example.com</email>
           </author>
       </entry>
   </feed>*/

	public function atom(){
		$atom ='<?xml version="1.0" encoding="utf-8"?>';
		$atom .='<feed xmlns="http://www.w3.org/2005/Atom" ';
		$atom .='xmlns:slash="http://purl.org/rss/1.0/modules/slash/" ';
		$atom .='xmlns:syn="http://purl.org/rss/1.0/modules/syndication/" ';
		$atom .='xml:lang="en">';
		$atom .='<title>'.$this->is["title"].' Feed</title>';
		$atom .='<subtitle>This is subtitle Feed</subtitle>';
		$atom .='<link href="'.SITE_URL.'/feed/" rel="self" />';
		$atom .='<link href="'.SITE_URL.'" />';
		$atom .='<id>urn:uuid:60a76c80-d399-11d9-b91C-0003939e0af6</id>';
		$atom .='<updated>'.date("Y-m-d").'T'.date("H-i-s").'Z</updated>';

		/*   $atom .='<link rel="alternate" type="text/html" href="http://example.org/2003/12/13/atom03.html"/>';
		 $atom .='<updated>'.date ("F d Y", filemtime($file)).'T'.date ("H:i:s", filemtime($file)).'Z</updated>';
		 $atom .='<rights><name>Copyright (c) 2013, Nikos Drosakis</name></rights>';
		 $atom .='<id>urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6</id>';
		*/
//post
		for ($i=0; $i <count($this->postlist); $i++){
				$atom .='<entry>';
				$atom .='<title>'.clean($this->postlist[$i]["title"]).'</title>';
				$atom .='<link href="'.clean(SITE_URL.$this->postlist[$i]["uri"]).' atom"/>';
				$atom .='<updated>'.date("Y-m-d",$this->postlist[$i]["modified"]).'T'.date("H:i:s",$this->postlist[$i]["modified"]).'Z</updated>';
				$atom .='<summary>'.clean($this->postlist[$i]["seodescription"]).'</summary>';
				$atom .='<content type="xhtml" xml:lang="el" xml:base="'.clean($this->postlist[$i]["uri"]).'">';
				$atom .='<div xmlns="http://www.w3.org/1999/xhtml">'.json_decode($this->postlist[$i]["tags"],true).'</div></content>';
				$atom .='</entry>';
		}
//user
		for ($i=0; $i <count($this->userlist); $i++){
				$atom .='<entry>';
				$atom .='<title>'.clean($this->userlist[$i]["name"]).'</title>';
				$atom .='<link href="'.clean(SITE_URL.$this->userlist[$i]["name"]).' atom"/>';
				$atom .='<updated>'.date("Y-m-d",$this->userlist[$i]["modified"]).'T'.date("H:i:s",$this->userlist[$i]["modified"]).'Z</updated>';
				$atom .='<summary>'.clean($this->userlist[$i]["seodescription"]).'</summary>';
				$atom .='<content type="xhtml" xml:lang="el" xml:base="'.clean($this->userlist[$i]["name"]).'"></content>';
				$atom .='</entry>';
		}

		$atom .='</feed>';
		return $atom;
	}

	public function rss(){

		//----------------------------RSS-------------
		/*	RSS TEMPLATE
       <?xml version="1.0" encoding="UTF-8" ?>
       <rss version="2.0">
       <channel>
         <title>W3Schools Home Page</title>
         <link>http://www.w3schools.com</link>
         <description>Free web building tutorials</description>
         <item>
           <title>RSS Tutorial</title>
           <link>http://www.w3schools.com/rss</link>
           <description>New RSS tutorial on W3Schools</description>
         </item>
       </channel>
       </rss>

		*/
		$rss ='<?xml version="1.0" encoding="UTF-8" ?>';
		$rss .='<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">';
		$rss .='<channel>';
		$rss .='<title>'.clean($this->is["title"]).'</title>';
		$rss .='<link>'.SITE_URL.'</link>';
		$rss .='<description>'.clean($this->is["seodescription"]).'</description>';

//post
		for ($i=0; $i <count($this->postlist); $i++){
				$rss .='<title>'.clean($this->postlist[$i]["title"]).'</title>';
				$rss .='<link>'.clean(SITE_URL.$this->postlist[$i]["uri"]).'</link>';
				$rss .='<description>'.clean($this->postlist[$i]["seodescription"]).'</description>';

		}

//user
		for ($i=0; $i <count($this->userlist); $i++){
			$rss .='<title>'.clean($this->user[$i]["name"]).'</title>';
			$rss .='<link>'.clean(SITE_URL.$this->user[$i]["uri"]).'</link>';
			$rss .='<description>'.clean($this->user[$i]["seodescription"]).'</description>';
		}

		$rss .='<atom:link href="'.SITE_URL.'rss.xml" rel="self" type="application/rss+xml" />';
		$rss .='</channel></rss>';

	return $rss;
	}

	public function create_xml($file){
			file_put_contents(SITE_ROOT.$file.'.xml',$this->$file());
			chmod(SITE_ROOT.$file.'.xml',0777);
	}

	public function clean($data){
		//remove &
		return str_replace('&','-',$data);
	}
}