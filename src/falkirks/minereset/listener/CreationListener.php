<?php
namespace falkirks\minereset\listener;


use falkirks\minereset\MineReset;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CreationListener implements Listener {
    /** @var  MineReset */
    private $api;

    /** @var  MineCreationSession[] */
    private $sessions;


    /**
     * CreationListener constructor.
     * @param MineReset $api
     */
    public function __construct(MineReset $api){
        $this->api = $api;
        $this->sessions = [];
    }

    /**
     * @priority LOW
     * @ignoreCancelled true
     *
     * @param PlayerInteractEvent $event
     */
    public function onBlockTap(PlayerInteractEvent $event){
        if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK){
            return;
        }

        $session = $this->getPlayerSession($event->getPlayer());

        if($session !== null){
            if($session->getLevel() === null || $session->getLevel()->getId() === $event->getPlayer()->getLevel()->getId()) {
                $session->setNextPoint($event->getBlock());
                $session->setLevel($event->getPlayer()->getPosition()->getLevel());

                if($session->canGenerate()){
                    $mine = $session->generate($this->getApi()->getMineManager());
                    $event->getPlayer()->sendMessage("Tạo thành công khu mine có tên " . $mine->getName() . ".");
                    $event->getPlayer()->sendMessage("BẮT BUỘC: Sử dụng /mine set " . $mine->getName() . " <block và tỉ lệ chiếm> để set block cho khu mine.");
                    unset($this->sessions[array_search($session, $this->sessions)]);
                }
                else{
                    $event->getPlayer()->sendMessage("Chạm vào một block khác để đặt điểm B.");
                }
            }
            else{
                $event->getPlayer()->sendMessage(TextFormat::RED . "Không thể tạo khu mine. Lí do: Bạn không ở trong world đang tạo.". TextFormat::RESET);
                unset($this->sessions[array_search($session, $this->sessions)]);
            }
        }
    }

    /**
     * @return MineReset
     */
    public function getApi(): MineReset{
        return $this->api;
    }

    public function playerHasSession(Player $player): bool {
        foreach ($this->sessions as $session){
            if($session->getPlayer()->getName() === $player->getName()){
                return true;
            }
        }
        return false;
    }

    public function getPlayerSession(Player $player){
        foreach ($this->sessions as $session){
            if($session->getPlayer()->getName() === $player->getName()){
                return $session;
            }
        }
        return null;
    }


    public function addSession(MineCreationSession $session): bool {
        if(!$this->playerHasSession($session->getPlayer())) {
            $this->sessions[] = $session;
            return true;
        }
        return false;
    }


}