<?php
namespace App\Corporations\CreepyPastaMachine\Transitions;

use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkCorporation;
use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkProject;
use LaravelNeuro\LaravelNeuro\Networking\TuringStrip;
use LaravelNeuro\LaravelNeuro\Networking\Transition;
use LaravelNeuro\LaravelNeuro\Pipeline;
use LaravelNeuro\LaravelNeuro\Prompts\IVFSprompt;
use LaravelNeuro\LaravelNeuro\Enums\TuringMove;

use App\Corporations\CreepyPastaMachine\Config;
use App\Corporations\CreepyPastaMachine\Database\Models\BlogArticle;
use App\Corporations\CreepyPastaMachine\Database\Models\ScpWarning;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

Class ScpVO extends Transition
{
    /**
    * An Eloquent Collection instance of the active project Model.
    * @var NetworkProject
    */
    protected NetworkProject $project;
    protected ScpWarning $ScpWarning;
    protected $promptSettings;
    protected $warningVOtext;

    /**
    * An Eloquent Collection instance of the active corporation Model.
    * This instance will Eager Load units belonging to this corporation (accessible with the dynamic property 'units').
    * Each unit will also Eager Load agents and dataSetTemplates belonging to it (accessible with the dynamic properties 'agents' and 'dataSetTemplates')
    * Each dataSetTemplate will also Eager Load dataSets belonging to it (accessible with the dynamic property 'dataSets')
    * Example of accessing a dataSet: $this->corporation->units->first()->dataSetTemplates->first()->dataSets->first();
    * You can use Eloquent to traverse the Eager Loaded relations.
    * @var NetworkCorporation
    */
    protected NetworkCorporation $corporation;

    public function __construct(int $projectId, TuringStrip $head, Collection $models)
    {
        parent::__construct($projectId, $head, $models);
        
        /**
         * Agent Name: SCPclerk
         * attaches this agent to this Transition instance.
         */
        $this->setAgentById(Config::TRANSITION_SCPCLERK_AGENT);
    }

    /**
     * Allows the modification of the AI agent's Pipeline to, for example: Change the model or the API link conditionally, or to pass additional headers such as an API key.
     * @method
     */
    protected function modifyPipeline(Pipeline $pipeline) : Pipeline
    {
        return $pipeline;
    }

    /**
     * Receives a fresh prompt instance, which allows, for example, the addition of chat-completion examples using pushUser() and pushAgent() on $prompt, if it is an SUAprompt object (the default).
     * Once preProcessInput has been called, if $prompt is an SUAprompt, a final pushUser() method will be applied to $prompt to append $this->head->getData() as the active request
     * @method
     */
    protected function preProcessPrompt($prompt)
    {
        return $prompt;
    }

    /**
     * Allows manipulation of $this->head->data before the data is attached to the SUAprompt using pushUser() as well as the implementation of additional logic at this point.
     * @method
     */
    protected function preProcessInput(string $data) : string
    {
        $this->promptSettings = $data;
        return $data;
    }

    /**
     * Receives the finalized prompt instance before it is passed to the agent pipeline, which allows manipulation of the prompt content.
     * The prompt class depends on the agent settings and should be maintained.
     * @method
     */
    protected function postProcessPrompt($prompt)
    {
        $processedPrompt = $prompt;

        // Implement your post-processor logic here, modifying $processedPrompt and executing other logic as needed.
        // To avoid unpredictable behavior, it is recommended to maintain type integrity between input and outpot, which the check below ensures.

        $blogID = BlogArticle::where('project_id', $this->project->id)
            ->latest()
            ->first()   
            ->id;

        $SCPen = ScpWarning::where('blog_id', $blogID)
                            ->where('lang', 'en')
                            ->first();

        if(empty($SCPen->vo_file))
        {
            
            $this->head->setNext(TuringMove::REPEAT);
            $this->ScpWarning = ScpWarning::where('blog_id', $blogID)
                                            ->where('lang', 'en')
                                            ->latest()
                                            ->first();

            $ClearanceLevels = [
                "Level 1" => "Unrestricted",
                "Level 2" => "Restricted",
                "Level 3" => "Confidential",
                "Level 4" => "Secret",
                "Level 5" => "Top Secret",
                "Level 6" => "Cosmic Top Secret"
            ];                                        

            $this->warningVOtext =  
                                "SCP Foundation File: SCP-anomalous-blog-$blogID.\n" .
                                "Clearance: " . $this->ScpWarning->clearance ." (".$ClearanceLevels[$this->ScpWarning->clearance].").\n" . 
                                "Containment-class: " . $this->ScpWarning->containment . ".\n" .             
                                "Risk-class: " . $this->ScpWarning->risk . ".\n" .
                                "Disruption-class: " . $this->ScpWarning->disruption . ".\n" .
                                "Threat-level: " . $this->ScpWarning->threat . ".\n" .
                                "Full assessment: \n";
        }
        else
        {
            $this->head->setNext(TuringMove::NEXT); 
            $this->ScpWarning = ScpWarning::where('blog_id', $blogID)
                                            ->where('lang', 'de')
                                            ->latest()
                                            ->first();   

            $ClearanceLevels = [
                "Level 1" => "Uneingeschränkt",
                "Level 2" => "Eingeschränkt",
                "Level 3" => "Vertraulich",
                "Level 4" => "Geheim",
                "Level 5" => "Streng geheim",
                "Level 6" => "Kosmisch streng geheim"
            ];                                        

            $this->warningVOtext =  
                                "SCP-Foundation Akte: SCP-Anomalie-Blog-$blogID.\n" .
                                "Geheimhaltungsgrad: " . $this->ScpWarning->clearance ." (".$ClearanceLevels[$this->ScpWarning->clearance].").\n" . 
                                "Eindämmungsstufe: " . $this->ScpWarning->containment . ".\n" .             
                                "Risikofaktor: " . $this->ScpWarning->risk . ".\n" .
                                "Unterbrechungsgrad: " . $this->ScpWarning->disruption . ".\n" .
                                "Gefahrenstufe: " . $this->ScpWarning->threat . ".\n" .
                                "Ermittlungsstand: \n";
        }

        if($processedPrompt instanceof IVFSprompt)
        {
            $processedPrompt->setInput($this->warningVOtext . $this->ScpWarning->assessment);
        }
        else
        {
            throw new \Exception("This Transition should only receive IVFSprompt prompt instances.");
        }

        if (
            gettype($prompt) !== gettype($processedPrompt) &&
            (is_object($prompt) && get_class($prompt) !== get_class($processedPrompt))
            ) {
            throw new \InvalidArgumentException("The type of the processed prompt must match the type of the input prompt.");
        }

        return $processedPrompt;
    }

    /**
     * Allows manipulation of $this->head->data after the prompt has been executed and output written to $this->head->data as well as the implementation of additional logic at this point.
     * @method
     */
    protected function postProcessOutput(string $data) : string
    {
        $blogEntry = BlogArticle::where('project_id', $this->project->id)->latest()->first();
        $fileMeta = json_decode($data);
        $file = Storage::disk($fileMeta->diskName)->get($fileMeta->fileName);
        $path = 'LaravelNeuro/CreepyPastaMachine/audio/article_'.$blogEntry->id.'_scp_vo_'.$this->ScpWarning->lang.'.mp3';
        Storage::disk('public')->put($path, $file);
        $file = Storage::disk($fileMeta->diskName)->delete($fileMeta->fileName);
        $this->ScpWarning->vo_file = $path;
        $this->ScpWarning->save();

        return $data;
    }
}