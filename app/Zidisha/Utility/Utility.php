<?php

namespace Zidisha\Utility;


use Faker\Provider\DateTime;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Zidisha\Country\Country;
use Zidisha\Country\CountryQuery;

class Utility {

    public static function getCountryByIP(){
        $ip = \Request::getClientIp();
        $country = new Country();
        $country->setId(null)
            ->setCountryCode(null)
            ->setName(null);

        if ($ip) {
            $reader = new Reader( app_path() . '/storage/external/GeoLite2-Country.mmdb');
            try {
                $record = $reader->country($ip);
                $country = CountryQuery::create()->findOneByCountryCode($record->country->isoCode);
            } catch (AddressNotFoundException $e) {
            }
        }
        return $country;
    }

    /**
     * Truncates text.
     *
     * Cuts a string to the length of $length and replaces the last characters
     * with the ellipsis if the text is longer than length.
     *
     * ### Options:
     *
     * - `ellipsis` Will be used as Ending and appended to the trimmed string (`ending` is deprecated)
     * - `exact` If false, $text will not be cut mid-word
     * - `html` If true, HTML tags would be handled correctly
     *
     * @param string $text String to truncate.
     * @param integer $length Length of returned string, including ellipsis.
     * @param array $options An array of html attributes and options.
     * @return string Trimmed string.
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html#TextHelper::truncate
     */
    public static function truncate($text, $length = 100, $options = array()) {
        $default = array(
            'ellipsis' => '...', 'exact' => true, 'html' => false
        );
        if (isset($options['ending'])) {
            $default['ellipsis'] = $options['ending'];
        } elseif (!empty($options['html'])) {
            $default['ellipsis'] = "\xe2\x80\xa6";
        }
        $options = array_merge($default, $options);
        $ellipsis = $options['ellipsis'];
        $exact = $options['exact'];
        $html = $options['html'];

        if (!function_exists('mb_strlen')) {
            class_exists('Multibyte');
        }

        if ($html) {
            if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            $totalLength = mb_strlen(strip_tags($ellipsis));
            $openTags = array();
            $truncate = '';

            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
            foreach ($tags as $tag) {
                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                    if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                        array_unshift($openTags, $tag[2]);
                    } elseif (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                        $pos = array_search($closeTag[1], $openTags);
                        if ($pos !== false) {
                            array_splice($openTags, $pos, 1);
                        }
                    }
                }
                $truncate .= $tag[1];

                $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
                if ($contentLength + $totalLength > $length) {
                    $left = $length - $totalLength;
                    $entitiesLength = 0;
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entitiesLength <= $left) {
                                $left--;
                                $entitiesLength += mb_strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }

                    $truncate .= mb_substr($tag[3], 0, $left + $entitiesLength);
                    break;
                } else {
                    $truncate .= $tag[3];
                    $totalLength += $contentLength;
                }
                if ($totalLength >= $length) {
                    break;
                }
            }
        } else {
            if (mb_strlen($text) <= $length) {
                return $text;
            }
            $truncate = mb_substr($text, 0, $length - mb_strlen($ellipsis));
        }
        if (!$exact) {
            $spacepos = mb_strrpos($truncate, ' ');
            if ($html) {
                $truncateCheck = mb_substr($truncate, 0, $spacepos);
                $lastOpenTag = mb_strrpos($truncateCheck, '<');
                $lastCloseTag = mb_strrpos($truncateCheck, '>');
                if ($lastOpenTag > $lastCloseTag) {
                    preg_match_all('/<[\w]+[^>]*>/s', $truncate, $lastTagMatches);
                    $lastTag = array_pop($lastTagMatches[0]);
                    $spacepos = mb_strrpos($truncate, $lastTag) + mb_strlen($lastTag);
                }
                $bits = mb_substr($truncate, $spacepos);
                preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                if (!empty($droppedTags)) {
                    if (!empty($openTags)) {
                        foreach ($droppedTags as $closingTag) {
                            if (!in_array($closingTag[1], $openTags)) {
                                array_unshift($openTags, $closingTag[1]);
                            }
                        }
                    } else {
                        foreach ($droppedTags as $closingTag) {
                            $openTags[] = $closingTag[1];
                        }
                    }
                }
            }
            $truncate = mb_substr($truncate, 0, $spacepos);
        }
        $truncate .= $ellipsis;

        if ($html) {
            foreach ($openTags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }

    public static function nestedArray($data, $separator = '_')
    {
        $nestedData = [];

        foreach ($data as $k => $v) {
            $keys = explode($separator, $k);
            $count = count($keys);
            $parent = & $nestedData;
            foreach ($keys as $key) {
                if ($count == 1) {
                    $parent[$key] = $v;
                } else {
                    if (!isset($parent[$key])) {
                        $parent[$key] = [];
                    }
                    $parent = & $parent[$key];
                }
                $count -= 1;
            }
        }

        return $nestedData;
    }

    public static function toInputNames($data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            $result[str_replace('.', '_', $key)] = $value;
        }

        return $result;
    }

    public static function fromInputNames($data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            $result[str_replace('_', '.', $key)] = $value;
        }

        return $result;
    }
    
    public static function clearPost($post_val) // remove email header injects
    {
        $injection_strings = array(
            "'apparently-to' i",
            "'bcc:' i",
            "'cc:' i",
            "'to:' i",
            "'boundary=' i",
            "'charset:' i",
            "'content-disposition' i",
            "'content-type' i",
            "'content-transfer-encoding' i",
            "'errors-to' i",
            "'in-reply-to' i",
            "'message-id' i",
            "'mime-version' i",
            "'multipart/mixed' i",
            "'multipart/alternative' i",
            "'multipart/related' i",
            "'reply-to:' i",
            "'x-mailer' i",
            "'x-sender' i",
            "'x-uidl' i"
        );

        $replace_strings = array(
            "apparently_to",
            "bcc_:",
            "cc_:",
            "to_:",
            "boundary_=",
            "charset_:",
            "content_disposition",
            "content_type",
            "content_transfer_encoding",
            "errors_to",
            "in_reply_to",
            "message_id",
            "mime_version",
            "multipart_mixed",
            "multipart_alternative",
            "multipart_related",
            "reply_to:",
            "x_mailer",
            "x_sender",
            "x_uidl"
        );


        $post_val = preg_replace($injection_strings, $replace_strings, $post_val);


        return $post_val;
    }

    // TODO pass country object
    public static function formatNumber($phoneNumber, $countryCode){
        $result=preg_replace("/[^0-9]+/", "", $phoneNumber);
        $country = CountryQuery::create()
            ->getOneByCountryCode($countryCode);
        $number = "";

        if ($country) {
            $number = substr($result, -($country->getPhoneNumberLength()));
            $number=str_pad($number, 13, '+'.$country->getDialingCode(), STR_PAD_LEFT);
        }
        return $number;
    }
}
