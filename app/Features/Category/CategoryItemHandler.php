<?php

namespace App\Features\Category;

use App\Classes\Callbacks\Handler;
use App\Classes\Html;
use App\Facades\EmojisushiApi;
use App\Facades\Hydrator;
use App\Objects\Product;
use App\Objects2\CallbackHandler;
use App\Objects2\CartCountMsg;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Telegram\Bot\FileUpload\InputFile;

class CategoryItemHandler extends Handler
{
    protected string $name = "category_item";

    public function run()
    {
        Validator::validate($this->arguments, [
            'page' => 'required|integer|min:1'
        ]);

        if ($this->arguments['page'] > 1) {
            $deleteMsg = $this->user->state->delete_msg_in_category;
            if ($deleteMsg) {

                $this->api->deleteMessage([
                    'chat_id' => $this->getUpdate()->getChat()->id,
                    'message_id' => $deleteMsg->id
                ]);

                $this->user->state->callback_handler = null;
                $this->user->save();
            }
        }

        $limit = Config::get('mall.settings.products.per_page', 10);
        $offset = ($this->arguments['page'] - 1) * $limit;

        $category = EmojisushiApi::getCategory(['id' => $this->arguments['id']]);

        $products = EmojisushiApi::getProducts([
            'category_slug' => $category->slug,
            'offset' => $offset,
            'limit' => $limit
        ])->data;

        collect($products)->each(function (Product $product) {
            $this->sendProduct($product);
        });

        $cart = EmojisushiApi::getCart();

        $message = $this->replyWithMessage([
            'text' => \Lang::get('lang.telegram.texts.triple_dot'),
            'reply_markup' => (new CategoryFooterKeyboard(
                $this->arguments['id'],
                $this->arguments['page'],
                $cart
            ))->getKeyboard()
        ]);

        $cartCountMsg = Hydrator::hydrate(CartCountMsg::class, [
            'id' => $message->messageId,
            'page' => $this->arguments['page'],
            'count'=> count($cart->data),
            'category_id' => $this->arguments['id']
        ]);

        $callbackHandler = Hydrator::hydrate(CallbackHandler::class, [
            'id' => $message->messageId,
        ]);

        $this->user->state->callback_handler = $callbackHandler;
        $this->user->state->cart_count_msg = $cartCountMsg;
        $this->user->save();
    }



    public function sendProduct(Product $product)
    {
        $caption = sprintf(
            "<b>%s</b>\n\n%s",
            $product->name,
            Html::strip($product->description_short)
        );

        if(\Cache::has("telegram.files." . $product->id)) {
            $this->replyWithPhoto([
                'photo' => \Cache::get("telegram.files." . $product->id),
                'caption' => $caption,
                'reply_markup' => (new CategoryProductKeyboard($product))->getKeyboard(),
                'parse_mode' => 'html',
            ]);
        } else if(isset($product->image_sets[0]->images[0]->path)) {
            $response = $this->replyWithPhoto([
                'photo' => InputFile::create($product->image_sets[0]->images[0]->path),
                'caption' => $caption,
                'reply_markup' => (new CategoryProductKeyboard($product))->getKeyboard(),
                'parse_mode' => 'html',
            ]);

            \Cache::set(
                "telegram.files." . $product->id,
                $response->getPhoto()->last()['file_id']
            );
        } else {
            $this->replyWithMessage([
                'text' => $caption,
                'reply_markup' => (new CategoryProductKeyboard($product))->getKeyboard(),
                'parse_mode' => 'html',
            ]);
        }
    }
}
