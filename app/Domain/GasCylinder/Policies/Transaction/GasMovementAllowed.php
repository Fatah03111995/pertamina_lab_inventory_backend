<?php

namespace App\Domain\GasCylinder\Policies\Transaction;

use App\Enums\GasCylinderStatus;
use App\Enums\GasTransactionType;
use App\Enums\GasLocationCategory;

final class GasMovementAllowed
{
    public function isStatusChangeAllowed(
        GasTransactionType $transactionType,
        GasCylinderStatus $from,
        GasCylinderStatus $to,
    ): bool {
        $rules = [

            //USING, MOVEMENT
            GasTransactionType::MARK_EMPTY->value => [
                'from' => [
                    GasCylinderStatus::FILLED,
                    GasCylinderStatus::IN_USE,
                    GasCylinderStatus::EMPTY
                ],
                'to' => [
                    GasCylinderStatus::EMPTY
                ]
            ],
            GasTransactionType::MOVEMENT->value => [
                'from' => [
                    GasCylinderStatus::FILLED,
                    GasCylinderStatus::IN_USE,
                    GasCylinderStatus::EMPTY
                ],
                'to' => [
                    GasCylinderStatus::FILLED,
                    GasCylinderStatus::IN_USE,
                    GasCylinderStatus::EMPTY
                ]
            ],

            //MAINTENANCE
            GasTransactionType::TAKE_FOR_MAINTENANCE->value => [
                'from' => [
                    GasCylinderStatus::FILLED,
                    GasCylinderStatus::IN_USE,
                    GasCylinderStatus::EMPTY,
                    GasCylinderStatus::IN_USE,
                    GasCylinderStatus::REFILL_PROCESS,

                ],
                'to' => [
                    GasCylinderStatus::MAINTENANCE
                ]
            ],
            GasTransactionType::RETURN_FROM_MAINTENANCE->value => [
                'from' => [
                    GasCylinderStatus::MAINTENANCE
                ],
                'to' => [
                    GasCylinderStatus::FILLED,
                    GasCylinderStatus::IN_USE,
                    GasCylinderStatus::EMPTY,
                    GasCylinderStatus::LOST,
                    GasCylinderStatus::REFILL_PROCESS,
                ]
            ],

            //REFILL
            GasTransactionType::TAKE_FOR_REFILL->value => [
                'from' => [
                ],
                'to' => [
                    GasCylinderStatus::REFILL_PROCESS
                ]
            ],
            GasTransactionType::RETURN_FROM_REFILL->value => [
                'from' => [
                    GasCylinderStatus::REFILL_PROCESS
                ],
                'to' => [
                    GasCylinderStatus::FILLED,
                    GasCylinderStatus::IN_USE,
                    GasCylinderStatus::LOST,
                    GasCylinderStatus::MAINTENANCE,
                ]
            ],

            //ISSUE
            GasTransactionType::REPORT_LOST->value => [
                'from' => [
                    GasCylinderStatus::FILLED,
                    GasCylinderStatus::IN_USE,
                    GasCylinderStatus::EMPTY,
                    GasCylinderStatus::MAINTENANCE,
                    GasCylinderStatus::REFILL_PROCESS,
                ],
                'to' => [
                    GasCylinderStatus::LOST
                ]
            ],
            GasTransactionType::RESOLVE_ISSUE->value => [
                'from' => [
                    GasCylinderStatus::FILLED,
                    GasCylinderStatus::IN_USE,
                    GasCylinderStatus::EMPTY,
                    GasCylinderStatus::MAINTENANCE,
                    GasCylinderStatus::LOST,
                    GasCylinderStatus::REFILL_PROCESS,
                ],
                'to' => [
                    GasCylinderStatus::FILLED,
                    GasCylinderStatus::IN_USE,
                    GasCylinderStatus::EMPTY,
                    GasCylinderStatus::MAINTENANCE,
                    GasCylinderStatus::REFILL_PROCESS,
                ]
            ]
        ];

        if (!isset($rules[$transactionType->value])) {
            return false;
        }

        return in_array($from, $rules[$transactionType->value]['from'], true)
            && in_array($to, $rules[$transactionType->value]['to'], true);

    }

    public function isMoveChangeAllowed(
        GasTransactionType $transactionType,
        GasLocationCategory $from,
        GasLocationCategory $to,
    ): bool {
        $rules = [

            //USING, MOVEMENT
            GasTransactionType::MARK_EMPTY->value => [
                'from' => [
                    GasLocationCategory::CONSUMPTION,
                    GasLocationCategory::MAINTENANCE,
                ],
                'to' => [
                    GasLocationCategory::CONSUMPTION,
                    GasLocationCategory::MAINTENANCE,
                ]
            ],
            GasTransactionType::MOVEMENT->value => [
                'from' => [
                    GasLocationCategory::CONSUMPTION,
                    GasLocationCategory::STORAGE
                ],
                'to' => [
                    GasLocationCategory::CONSUMPTION,
                    GasLocationCategory::STORAGE
                ]
            ],

            //MAINTENANCE
            GasTransactionType::TAKE_FOR_MAINTENANCE->value => [
                'from' => [
                    GasLocationCategory::CONSUMPTION,
                    GasLocationCategory::REFILLING,
                    GasLocationCategory::STORAGE
                ],
                'to' => [
                    GasLocationCategory::MAINTENANCE
                ]
            ],
            GasTransactionType::RETURN_FROM_MAINTENANCE->value => [
                'from' => [
                    GasLocationCategory::MAINTENANCE
                ],
                'to' => [
                    GasLocationCategory::CONSUMPTION,
                    GasLocationCategory::REFILLING,
                    GasLocationCategory::STORAGE
                ]
            ],

            //REFILL
            GasTransactionType::TAKE_FOR_REFILL->value => [
                'from' => [
                    GasLocationCategory::STORAGE
                ],
                'to' => [
                    GasLocationCategory::REFILLING
                ]
            ],
            GasTransactionType::RETURN_FROM_REFILL->value => [
                'from' => [
                    GasLocationCategory::REFILLING
                ],
                'to' => [
                    GasLocationCategory::STORAGE
                ]
            ],

            //ISSUE
            GasTransactionType::REPORT_LOST->value => [
                'from' => [
                    GasLocationCategory::CONSUMPTION,
                    GasLocationCategory::MAINTENANCE,
                    GasLocationCategory::REFILLING,
                    GasLocationCategory::STORAGE,
                ],
                'to' => [
                    GasLocationCategory::CONSUMPTION,
                    GasLocationCategory::MAINTENANCE,
                    GasLocationCategory::REFILLING,
                    GasLocationCategory::STORAGE,
                ]
            ],
            GasTransactionType::RESOLVE_ISSUE->value => [
                'from' => [
                    GasLocationCategory::CONSUMPTION,
                    GasLocationCategory::MAINTENANCE,
                    GasLocationCategory::REFILLING,
                    GasLocationCategory::STORAGE,
                ],
                'to' => [
                    GasLocationCategory::CONSUMPTION,
                    GasLocationCategory::MAINTENANCE,
                    GasLocationCategory::REFILLING,
                    GasLocationCategory::STORAGE,
                ]
            ]
        ];

        if (!isset($rules[$transactionType->value])) {
            return false;
        }

        return in_array($from, $rules[$transactionType->value]['from'], true)
            && in_array($to, $rules[$transactionType->value]['to'], true);
    }
}
