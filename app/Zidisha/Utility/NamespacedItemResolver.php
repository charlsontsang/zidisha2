<?php
namespace Zidisha\Utility;

use Illuminate\Support\NamespacedItemResolver as LaravelNamespacedItemResolver;

class NamespacedItemResolver  extends LaravelNamespacedItemResolver{

    /**
     * Parse an array of basic segments.
     *
     * @param  array  $segments
     * @return array
     */
    protected function parseBasicSegments(array $segments)
    {
        // The first segment in a basic array will always be the group, so we can go
        // ahead and grab that segment. If there is only one total segment we are
        // just pulling an entire group out of the array and not a single item.
        $folder = $segments[0];

        if (count($segments) == 1)
        {
            return array(null, $folder, null, null);
        }

        elseif (count($segments) == 2)
        {
            $group = $segments[1];

            return array(null, $folder, $group, null);
        }

        // If there is more than one segment in this group, it means we are pulling
        // a specific item out of a groups and will need to return the item name
        // as well as the group so we know which item to pull from the arrays.
        else
        {

            $group = $segments[1];

            $item = implode('.', array_slice($segments, 2));

            return array(null, $folder, $group, $item);
        }
    }
}
