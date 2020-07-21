<?php

/* LỆNH EDIT BỊ LỖI ANH EM ƠI, KHI NÀO CÓ THỜI GIAN MÌNH SỬA */

namespace falkirks\minereset\command;


use falkirks\minereset\task\AboutPullTask;
use Frago9876543210\EasyForms\elements\custom\Dropdown;
use Frago9876543210\EasyForms\elements\custom\Input;
use Frago9876543210\EasyForms\elements\custom\Label;
use Frago9876543210\EasyForms\forms\CustomForm;
use Frago9876543210\EasyForms\forms\ModalForm;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EditCommand extends SubCommand{
    public function execute(CommandSender $sender, $commandLabel, array $args){
        if($sender->hasPermission("minereset.command.edit")) {
            if($sender instanceof Player && $this->formsSupported()){
                $sender->sendForm(new class("Mine: a", [
                    new Dropdown("Chọn sản phẩm", ["beer", "cheese", "cola"]),
                    new Input("Tên khu mine", "a"),
                    new Input("Thời gian đặt lại", "-1"),
                    new Label("Thời gian đặt lại được tính bằng giây"), //popElement() does not work with label
                    new Input("Tên warp", ""),
                    new Label("Tên warp để kết nối với khu mine."),
                ]) extends CustomForm {
                    public function onSubmit(Player $player, $response) : void{
                        parent::onSubmit($player, $response);
                        $player->sendMessage("Tuyệt!");
                    }
                });
            }
            else {
                $sender->sendMessage(TextFormat::RED . "Bạn phải cài đặt EasyForms để sử dụng lệnh này!" . TextFormat::RESET);
            }
        }
        else{
            $sender->sendMessage(TextFormat::RED . "Bạn không có quyền để sử dụng lệnh này!" . TextFormat::RESET);
        }


    }
}