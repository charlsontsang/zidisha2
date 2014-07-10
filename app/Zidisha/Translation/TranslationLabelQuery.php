<?php

namespace Zidisha\Translation;

use Zidisha\Translation\Base\TranslationLabelQuery as BaseTranslationLabelQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'translation_labels' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class TranslationLabelQuery extends BaseTranslationLabelQuery
{

    public function getTotals()
    {
        return $this
            ->select(array('filename', 'totalUntranslated', 'totalUpdated'))
            ->withColumn('SUM(CASE WHEN translated THEN 0 ELSE 1 END)', 'totalUntranslated')
            ->withColumn('SUM(CASE WHEN updated THEN 1 ELSE 0 END)', 'totalUpdated')
            ->groupByFilename()
            ->find();
    }

} // TranslationLabelQuery
