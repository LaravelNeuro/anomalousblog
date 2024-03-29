<?php
namespace App\Corporations\CreepyPastaMachine\Transitions;

use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkCorporation;
use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkProject;
use LaravelNeuro\LaravelNeuro\Networking\TuringStrip;
use LaravelNeuro\LaravelNeuro\Networking\Transition;
use LaravelNeuro\LaravelNeuro\Pipeline;

use App\Corporations\CreepyPastaMachine\Config;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

Class Photographer extends Transition
{
    /**
    * An Eloquent Collection instance of the active project Model.
    * @var NetworkProject
    */
    protected NetworkProject $project;

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
         * Agent Name: Photographer
         * attaches this agent to this Transition instance.
         */
        $this->setAgentById(Config::TRANSITION_PHOTOGRAPHER_AGENT);
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

        $processedPrompt->setPrompt($this->head->getData());

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
        $blogArticle = $this->models->get('BlogArticle')->newQuery()->latest()->first();

        $imageData = json_decode($data)[0];
        $image = Storage::disk($imageData->diskName)->get($imageData->fileName);
        $path = 'LaravelNeuro/CreepyPastaMachine/images/article_'.$blogArticle->id.'_img.png';
        Storage::disk('public')->put($path, $image);
        Storage::disk($imageData->diskName)->delete($imageData->fileName);

        $urlPath = Storage::disk('public')->url($path);
        $this->models->get('BlogArticle')->newQuery()->where('id', $blogArticle->id)->update([
        "articleEN" => preg_replace('/(src=".*?")/', 'src="'.$urlPath.'"', $blogArticle->articleEN),
        "articleDE" => preg_replace('/(src=".*?")/', 'src="'.$urlPath.'"', $blogArticle->articleDE) 
        ]);

        return $data;
    }
}