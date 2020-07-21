<?php
namespace falkirks\minereset\command;


use falkirks\minereset\exception\MineResetException;
use falkirks\minereset\Mine;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ResetAllCommand extends SubCommand{
    public function execute(CommandSender $sender, $commandLabel, array $args){
        if($sender->hasPermission("minereset.command.resetall")) {
            $success = 0;
            foreach ($this->getApi()->getMineManager() as $mine) {
                if ($mine instanceof Mine) {
                    try{
                        $mine->reset();
                        $success++;
                        $this->getApi()->getResetProgressManager()->addObserver($mine->getName(), $sender);
                    } catch (MineResetException $exception){
                        $sender->sendMessage(TextFormat::RED . "Có lỗi được phát hiện trong khu {$mine->getName()}, vui lòng kiểm tra lại file config." . TextFormat::RESET);
                    }

                }
            }
            $count = count($this->getApi()->getMineManager());
            $sender->sendMessage("KẾT QUẢ: {$success} khu được reset thành công trên tổng {$count} khu mine.");
        }
        else{
            $sender->sendMessage(TextFormat::RED . "Bạn không có quyền để sử dụng lệnh này!" . TextFormat::RESET);
        }
    }
}