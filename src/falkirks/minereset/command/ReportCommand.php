<?php

namespace falkirks\minereset\command;


use falkirks\minereset\task\AboutPullTask;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;

class ReportCommand extends SubCommand{
    public function execute(CommandSender $sender, $commandLabel, array $args){
        if($sender->hasPermission("minereset.command.report")) {
            $data = $this->getApi()->getDebugDumpFactory()->generate();
            if ($sender instanceof ConsoleCommandSender) {
                $issueContent = "\n\n(Giải thích vấn đề của bạn ở đây)\n\n```\n$data\n```";
                $url = "https://github.com/Falkirks/MineReset/issues/new" . (count($args) > 0 ? "?title=" . urlencode(implode(" ", $args)) . "\&" : "?") . "body=" . urlencode($issueContent);
                switch (Utils::getOS()) {
                    case 'win':
                        `start $url`;
                        break;
                    case 'mac':
                        `open $url`;
                        break;
                    case 'linux':
                        `xdg-open $url`;
                        break;
                    default:
                        $sender->sendMessage("Sao chép và dán URL sau vào trình duyệt của bạn để bắt đầu báo cáo.");
                        $sender->sendMessage("------------------");
                        $sender->sendMessage($url);
                        $sender->sendMessage("------------------");
                        break;
                }
            }
            $sender->sendMessage("--- Dữ liệu MineReset ---");
            $sender->sendMessage($data);
        }
        else{
            $sender->sendMessage(TextFormat::RED . "Bạn không có quyền để sử dụng lệnh này!" . TextFormat::RESET);
        }
    }
}