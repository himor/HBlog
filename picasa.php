    <?php
	$user = 'himor.cre';

$albumlist = array(
  'Boston, MA',
  'chicago',
  'Museum of Science and Industry',
  'California',
  'San Francisco, CA',
  'Miami 2010',
  'Miami 2009',
  'New York Feb-2009'
);

$album_select = array();
foreach (array_rand($albumlist, 5) as $key) :
 $album_select[] = $albumlist[$key];
endforeach;

// the feed URLs from where album and photo information will be fetched
$album_feed = 'http://picasaweb.google.com/data/feed/api/user/' . $user . '?v=2';
$photo_feed = 'http://picasaweb.google.com/data/feed/api/user/' . $user . '/albumid/';

// read album feed into a SimpleXML object
$albums = simplexml_load_file($album_feed);

$result = array();
foreach ($albums->entry as $album) :

  // if album is one of the chosen ones, continue
  if (in_array($album->title, $album_select)) :

    // get the number of photos for this album
    $photocount = (int) $album->children('http://schemas.google.com/photos/2007')->numphotos;

    // choose 2 random photos from this album
    $photo_select = array_rand(range(1, $photocount), 4);

    // get the ID of the current album
    $album_id = $album->children('http://schemas.google.com/photos/2007')->id;

    // read photo feed for this album into a SimpleXML object
    $photos = simplexml_load_file($photo_feed . $album_id . '?v=2');

    $i = 0;
    foreach ($photos->entry as $photo) :
      if (in_array($i, $photo_select)) :
        $temp = array();                    

        // get the photo and thumbnail information
        $media = $photo->children('http://search.yahoo.com/mrss/');

        // full image information
        $group_content = $media->group->content;
        $temp['full_url'] = $group_content->attributes()->{'url'};
        $temp['full_width'] = $group_content->attributes()->{'width'};
        $temp['full_height'] = $group_content->attributes()->{'height'};

        // thumbnail information, get the 3rd (=biggest) thumbnail version
        // change the [2] to [0] or [1] to get smaller thumbnails
        $group_thumbnail = $media->group->thumbnail[2];
        $temp['thumbnail_url'] = $group_thumbnail->attributes()->{'url'};
        //$temp['thumbnail_width'] = $group_thumbnail->attributes()->{'width'}*.21;
        //$temp['thumbnail_height'] = $group_thumbnail->attributes()->{'height'}*.21;
		$temp['thumbnail_width'] = 62;
        $temp['thumbnail_height'] = 45;

        $temp['album'] = $album->title[0];
        $result[] = $temp;
      endif;
      $i++;
    endforeach;

  endif;
endforeach;	

shuffle($result);

// display results
foreach ($result as $r) :
  /*echo '<a href="'.$r['full_url'].'" title="'.$r['album'].'"><img src="'.$r['thumbnail_url'].'" width="'.$r['thumbnail_width'].'" height="'.$r['thumbnail_height'].'" alt="'.$r['album'].'" /></a>';*/
  echo '<a href="https://picasaweb.google.com/101311003421553582752" title="'.$r['album'].'"><img src="'.$r['thumbnail_url'].'" width="'.$r['thumbnail_width'].'" height="'.$r['thumbnail_height'].'" alt="'.$r['album'].'" /></a>';
  
endforeach;

	?>