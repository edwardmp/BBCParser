<?php

namespace BBCParser;

use \DOMDocument;

/**
* BBCParser
*
* A PHP library that parses the BBC website's main modules into a organised array format.
*
* @author Edward Poot <edwardmp@gmail.com>
* @copyright 2014 Edward Poot
*/
class Parser
{
	const URL = 'http://www.bbc.co.uk';

	private $pageSource = NULL;

	private $modulesData = NULL;

	/**
	 * Parses the retrieved HTML, find's relevant module info and stores it in an array
	 *
	 * @return string
	 */
	public function __construct()
	{
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, self::URL);

        // return the curl result as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // save output of the curl operation
        $this->pageSource = curl_exec($ch);

        if (curl_errno($ch))
        	throw new Exception('CURL error: ' . curl_error($ch));

        // close curl resource
        curl_close($ch);

        return $this->pageSource;
	}

	/**
	 * Parses the pageSource, find's relevant module info, serializes and stores it in an array
	 *
	 * @return array
	 */
	public function parseAndSerializeData()
	{
		// BBC's HTMl appears to be malformed, prevent warnings
		libxml_use_internal_errors(true);

		// instantialize DOM structure, load pageSource
		$doc = new DOMDocument();
		$doc->loadHTML($this->pageSource);

		$divTags = $doc->getElementsByTagName('div');

		// first, let's loop through all div tags
		foreach ($divTags as $divTag)
		{
			// find all relevant modules that have the module class attribute
			if ($divTag->hasAttribute('class') && $divTag->getAttribute('class') === "module")
			{
				// this is the id tag of the div, which I use to determine the categories
				$moduleIdentifier = $divTag->getAttribute('id');

				foreach($divTag->getElementsByTagName('div') as $node)
				{
					if ($node->hasAttribute('class') && $node->getAttribute('class') === "container")
					{
						$summary = NULL;
						foreach($node->getElementsByTagName('p') as $paragraph)
						{
							// get the summary of the article
							$summary = trim($paragraph->textContent);

							// get all span tags that are child's of the paragraph
							$addedSpanTag = array();
					        foreach ($paragraph->getElementsByTagName('span') as $child)
					        	if (strlen(trim($child->textContent)) != 0)
						           $addedSpanTag[] = trim($child->textContent);

							// if any spantags are present, add it to the array
							if ($addedSpanTag)
								$this->modulesData[$moduleIdentifier]["spantags"][] = $addedSpanTag;
						}

						// loop through all dt tags
						foreach($node->getElementsByTagName('dt') as $dd)
						{
							// get all links that are children of the dt tag
							foreach($dd->getElementsByTagName('a') as $link)
							{
								$ddChildren["content"] = trim($dd->textContent);
								$ddChildren["href"] = trim($link->getAttribute('href'));

								$this->modulesData[$moduleIdentifier]["dt"] = $ddChildren;
							}
						}

						// get all link tag children of the module div
						foreach($node->getElementsByTagName('a') as $articles)
						{
							$articleTags = array();
							// if summary is present add it to array
							if ($summary)
							{
								$articleTags["summary"] = trim($summary);

								// only one summary per module, so set it to null
								$summary = NULL;
							}

							// loop through span tags
							foreach($articles->getElementsByTagName('span') as $span)
							{
								$title = trim($span->textContent);
								// if title is blank, we need to use the heads textContent instead
								if (strlen($title) == 0)
									$title = trim($articles->textContent);

								$articleTags["title"] = trim($title);
								$articleTags["link"] = $articles->getAttribute('href');

							}

							// loop through all images, if any are present
							foreach($articles->getElementsByTagName('img') as $img)
							{
								$image["src"] = $img->getAttribute('src');
								$image["alt"] = $img->getAttribute('alt');
								$articleTags["images"][] = $image;
							}

							// add articleTags metadata to values array, only if array is not empty
							if (!empty($articleTags))
								$this->modulesData[$moduleIdentifier]["links_and_images"][] = $articleTags;
						}
					}
				}
			}
	    }

		// remove empty modules
		array_filter($this->modulesData);

		// if there is no modulesData return false
		if (empty($this->modulesData))
			return false;

		return $this->modulesData;
	}

	/**
	 * Retrieves module info for given key
	 *
	 * @param string $moduleName the name of the module to be returned
	 * @return array
	 */
	public function returnDataForModule($moduleName = NULL)
	{
		if ($moduleName)
			return $this->modulesData[trim(strtolower($moduleName))];
	}
}
?>