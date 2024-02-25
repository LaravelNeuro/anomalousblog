<?php
namespace App\Corporations\CreepyPastaMachine;

use Illuminate\Support\Collection;

use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkCorporation;
use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkProject;
use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkHistory;
use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkState;
use LaravelNeuro\LaravelNeuro\Networking\Corporation;
use LaravelNeuro\LaravelNeuro\Networking\TuringStrip;

use LaravelNeuro\LaravelNeuro\Enums\StuckHandler;
use LaravelNeuro\LaravelNeuro\Enums\TuringState;
use LaravelNeuro\LaravelNeuro\Enums\TuringMove;
use LaravelNeuro\LaravelNeuro\Enums\TuringMode;
use LaravelNeuro\LaravelNeuro\Enums\TuringHistory;

use App\Corporations\CreepyPastaMachine\Config;
use App\Corporations\CreepyPastaMachine\Bootstrap;

use App\Corporations\CreepyPastaMachine\Transitions\NewsCrawler;
use App\Corporations\CreepyPastaMachine\Transitions\Validator;
use App\Corporations\CreepyPastaMachine\Transitions\Summarizer;
use App\Corporations\CreepyPastaMachine\Transitions\Paranormalist;
use App\Corporations\CreepyPastaMachine\Transitions\Writer;
use App\Corporations\CreepyPastaMachine\Transitions\BlogTranslator;
use App\Corporations\CreepyPastaMachine\Transitions\Formatter;
use App\Corporations\CreepyPastaMachine\Transitions\Photographer;
use App\Corporations\CreepyPastaMachine\Transitions\Assessor;
use App\Corporations\CreepyPastaMachine\Transitions\Arbiter;
use App\Corporations\CreepyPastaMachine\Transitions\AssessmentTranslator;
use App\Corporations\CreepyPastaMachine\Transitions\BlogVO;
use App\Corporations\CreepyPastaMachine\Transitions\ScpVO;
use LaravelNeuro\LaravelNeuro\Networking\Transition;

/**
* When creating an instance of CreepyPastaMachine, be sure to pass the $task parameter to its constructor: 
* __construct(string $task)
*
* Example: new CreepyPastaMachine("Perform the task defined in this string");
*
* Using the run() method on an instance of CreepyPastaMachine creates the Corporation runtime, during which a task is forwared to the state machine, evaluated and processed at every step, and, finally, consolidated into an output on the final state, after which the state machine shuts down and that output is passed to the loaded NetworkProject instance. This instance is saved to the database and then returned by the run() method.  
*
* Example: $myProject = new CreepyPastaMachine("Perform the task defined in this string");
*          $myProject->run;
*
*   At this point, $myProject contains an Eloquent Collection entry from the NetworkProject Model, representing the completed project. The resolution field contains the output data, and you can retrieve the following data from $myProject:
*       int $myProject->id
*       timestamp $myProject->created_at
*       timestamp $myProject->updated_at
*       int $myProject->corporation_id
*       string $myProject->task
*       string $myProject->resolution
*/
class CreepyPastaMachine extends Corporation {

    /**
     * The Corporation's namespace is required for various bootstrapping operations, such as loading local Models.
     *
     * @var string or bool
     */
    public $corporationNameSpace = 'App\Corporations\CreepyPastaMachine';

    /**
     * The number of intermediary steps to set up for your state machine. 
     * Your installation will fill this out based on the number of Transitions you have created, 
     * but you can create more Transitions and increment this number later.
     *
     * @var int
     */
    public int $states = Config::STATES;

    /**
     * The database id of this Corporation, used to instantiate it.
     *
     * @var int
     */
    public int $corporationId = Config::CORPORATION;

    /**
     * This array contains Eloquent Model instances of each Model in this Corporation's Database/Models folder, loaded in by the Bootstrap class.
     *
     * @var Collection
     */
    public Collection $models;

    /**
     * Can be set to REPEAT, CONTINUE, and TERMINATE
     * This setting determines the state machine's behavior when a Transition returns a TuringStrip with a TuringMode mode of STUCK  
     *
     * @var StuckHandler
     */
    public StuckHandler $stuckSetting = StuckHandler::REPEAT;

    protected function initial(TuringStrip $head) : TuringStrip
    {
        NetworkHistory::create([
            'project_id' => $this->project->id, 
            'entryType' => TuringHistory::PROMPT,
            'content' => $this->task
            ]);
        
        $transition = new NewsCrawler($this->project->id, $head, $this->models);

        return $transition->handle();
    }

    protected function continue(TuringStrip $head) : TuringStrip
    {
        
        switch($this->getHeadPosition()) {
			case 1:
				$transition = new Summarizer($this->project->id, $head, $this->models);
				break;
			case 2:
				$transition = new Paranormalist($this->project->id, $head, $this->models);
				break;
			case 3:
				$transition = new Writer($this->project->id, $head, $this->models);
				break;
			case 4:
				$transition = new BlogTranslator($this->project->id, $head, $this->models);
				break;
			case 5:
				$transition = new Formatter($this->project->id, $head, $this->models);
				break;
			case 6:
				$transition = new Photographer($this->project->id, $head, $this->models);
				break;
			case 7:
				$transition = new Assessor($this->project->id, $head, $this->models);
				break;
			case 8:
				$transition = new Arbiter($this->project->id, $head, $this->models);
				break;
			case 9:
				$transition = new AssessmentTranslator($this->project->id, $head, $this->models);
				break;
			case 10:
				$transition = new BlogVO($this->project->id, $head, $this->models);
				break;
			case 11:
				$transition = new ScpVO($this->project->id, $head, $this->models);
				break;
			default:
				$transition = new Transition($this->project->id, $head, $this->models);
				break;
		}

        return $transition->handle();
    }

    protected function final(TuringStrip $head) : TuringStrip
    {
        $transition = new Validator($this->project->id, $head, $this->models);

        return $transition->handle();
    }

}