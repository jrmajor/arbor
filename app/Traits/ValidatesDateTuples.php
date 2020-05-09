<?php

namespace App\Traits;

trait ValidatesDateTuples
{
    protected function prepareDateTuples($model)
    {
        foreach (forward_static_call([$model, 'getDateTuples']) as $dateTuple) {
            if (
                $this[$dateTuple.'_from'] != null
                && $this[$dateTuple.'_to'] == null
            ) {
                $this[$dateTuple.'_to'] = $this[$dateTuple.'_from'];
            }
        }
    }
}
