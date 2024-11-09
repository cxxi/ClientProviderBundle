<?php

namespace Cxxi\ClientProviderBundle\Enum;

enum AggregationLogicEnum: int
{
	case CONCAT = 0;
    case MERGE  = 1;
    case FILTER = 2;
}