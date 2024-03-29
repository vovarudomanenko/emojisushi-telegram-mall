<?php namespace App\Features\Cart;

use Telegram\Bot\Keyboard\Keyboard;

class CartEmptyKeyboard
{
    public function getKeyboard(): Keyboard
    {
       return (new Keyboard())->inline()->row([
           Keyboard::inlineButton([
               'text' => \Lang::get('lang.telegram.buttons.to_categories'),
               'callback_data' => json_encode(['category_items', []])
           ])
       ])->row([])->row([
           Keyboard::inlineButton([
               'text' => \Lang::get('lang.telegram.buttons.in_menu_main'),
               'callback_data' => json_encode(['start', []])
           ])
       ]);
    }

}
