<?php
declare(strict_types=1);

namespace Kata;

function echoln(string $string): void
{
    echo $string . "\n";
}

class Game
{
    /** @var list<string> */
    private array $players;
    /** @var array<int,int> */
    private array $places;
    /** @var array<int,int> */
    private array $purses;
    /** @var array<int,bool> */
    private array $inPenaltyBox;

    /** @var list<string> */
    private array $popQuestions;
    /** @var list<string> */
    private array $scienceQuestions;
    /** @var list<string> */
    private array $sportsQuestions;
    /** @var list<string> */
    private array $rockQuestions;

    private int $currentPlayer = 0;
    private bool $isGettingOutOfPenaltyBox = false;

    public function __construct()
    {

        $this->players = array();
        $this->places = array(0);
        $this->purses = array(0);
        $this->inPenaltyBox = array(0);

        $this->popQuestions = array();
        $this->scienceQuestions = array();
        $this->sportsQuestions = array();
        $this->rockQuestions = array();

        for ($i = 0; $i < 50; $i++) {
            array_push($this->popQuestions, "Pop Question " . $i);
            array_push($this->scienceQuestions, ("Science Question " . $i));
            array_push($this->sportsQuestions, ("Sports Question " . $i));
            array_push($this->rockQuestions, $this->createRockQuestion($i));
        }
    }

    private function createRockQuestion(int $index): string
    {
        return "Rock Question " . $index;
    }

    public function isPlayable(): bool
    {
        return ($this->howManyPlayers() >= 2);
    }

    public function add(string $playerName): bool
    {
        array_push($this->players, $playerName);
        $this->places[$this->howManyPlayers()] = 0;
        $this->purses[$this->howManyPlayers()] = 0;
        $this->inPenaltyBox[$this->howManyPlayers()] = false;

        echoln($playerName . " was added");
        echoln("They are player number " . count($this->players));
        return true;
    }

    public function howManyPlayers(): int
    {
        return count($this->players);
    }

    public function roll(int $roll): void
    {
        echoln($this->players[$this->currentPlayer] . " is the current player");
        echoln("They have rolled a " . $roll);

        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($roll % 2 != 0) {
                $this->isGettingOutOfPenaltyBox = true;

                echoln($this->players[$this->currentPlayer] . " is getting out of the penalty box");
                $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
                if ($this->places[$this->currentPlayer] > 11) {
                    $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;
                }

                echoln($this->players[$this->currentPlayer]
                    . "'s new location is "
                    . $this->places[$this->currentPlayer]);
                echoln("The category is " . $this->currentCategory());
                $this->askQuestion();
            } else {
                echoln($this->players[$this->currentPlayer] . " is not getting out of the penalty box");
                $this->isGettingOutOfPenaltyBox = false;
            }

        } else {

            $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
            if ($this->places[$this->currentPlayer] > 11) {
                $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;
            }

            echoln($this->players[$this->currentPlayer]
                . "'s new location is "
                . $this->places[$this->currentPlayer]);
            echoln("The category is " . $this->currentCategory());
            $this->askQuestion();
        }

    }

    private function askQuestion(): void
    {
        $category = $this->currentCategory();

        if ($category === "Pop") {
            echoln((string)array_shift($this->popQuestions));
        }
        if ($category === "Science") {
            echoln((string)array_shift($this->scienceQuestions));
        }
        if ($category === "Sports") {
            echoln((string)array_shift($this->sportsQuestions));
        }
        if ($category === "Rock") {
            echoln((string)array_shift($this->rockQuestions));
        }
    }

    private function currentCategory(): string
    {
        $space = $this->places[$this->currentPlayer];

        // Same mapping as before:
        // 0,4,8 => Pop; 1,5,9 => Science; 2,6,10 => Sports; 3,7,11 => Rock
        $mod = $space % 4;
        if ($mod === 0) {
            return "Pop";
        }
        if ($mod === 1) {
            return "Science";
        }
        if ($mod === 2) {
            return "Sports";
        }
        return "Rock";
    }

    public function wasCorrectlyAnswered(): bool
    {
        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($this->isGettingOutOfPenaltyBox) {
                echoln("Answer was correct!!!!");
                $this->purses[$this->currentPlayer]++;
                echoln($this->players[$this->currentPlayer]
                    . " now has "
                    . $this->purses[$this->currentPlayer]
                    . " Gold Coins.");

                $winner = $this->didPlayerWin();
                $this->currentPlayer++;
                if ($this->currentPlayer == count($this->players)) {
                    $this->currentPlayer = 0;
                }

                return $winner;
            } else {
                $this->currentPlayer++;
                if ($this->currentPlayer == count($this->players)) {
                    $this->currentPlayer = 0;
                }
                return true;
            }

        } else {

            echoln("Answer was corrent!!!!");
            $this->purses[$this->currentPlayer]++;
            echoln($this->players[$this->currentPlayer]
                . " now has "
                . $this->purses[$this->currentPlayer]
                . " Gold Coins.");

            $winner = $this->didPlayerWin();
            $this->currentPlayer++;
            if ($this->currentPlayer == count($this->players)) {
                $this->currentPlayer = 0;
            }

            return $winner;
        }
    }

    public function wrongAnswer(): bool
    {
        echoln("Question was incorrectly answered");
        echoln($this->players[$this->currentPlayer] . " was sent to the penalty box");
        $this->inPenaltyBox[$this->currentPlayer] = true;

        $this->currentPlayer++;
        if ($this->currentPlayer == count($this->players)) {
            $this->currentPlayer = 0;
        }
        return true;
    }

    private function didPlayerWin(): bool
    {
        return !($this->purses[$this->currentPlayer] == 6);
    }
}
