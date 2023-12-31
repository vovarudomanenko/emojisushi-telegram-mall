<?php

namespace Layerok\TgMall\Features\Checkout\Keyboard;

use Telegram\Bot\Keyboard\Keyboard;

class WishToAddSticksKeyboard {
    public function getKeyboard(): Keyboard {
        return (new Keyboard())->inline()->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.yes'),
                'callback_data' => json_encode([
                    'add_sticks', []
                ])
            ]),
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.no'),
                'callback_data' => json_encode([
                    'wish_to_leave_comment', []
                ])
            ])
        ]);
    }
}
