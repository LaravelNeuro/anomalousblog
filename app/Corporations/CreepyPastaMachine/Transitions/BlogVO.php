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

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFMpeg;

Class BlogVO extends Transition
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
         * Agent Name: Blogger
         * attaches this agent to this Transition instance.
         */
        $this->setAgentById(Config::TRANSITION_BLOGGER_AGENT);
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

        $blogArticle = BlogArticle::where('project_id', $this->project->id)->latest()->first();

        if($processedPrompt instanceof IVFSprompt)
        {
            if(empty($blogArticle->articleENvo))
            {
                $readThis = preg_replace('/<img.+?>/', '', preg_replace('/\n<img.+?>\n/', '',            
                                            $blogArticle->articleENraw));
                $processedPrompt->setInput($readThis);
                $this->head->setNext(TuringMove::REPEAT);
            }
            else
            {
                $readThis = preg_replace('/<img.+?>/', '', preg_replace('/\n<img.+?>\n/', '',            
                                            $blogArticle->articleDEraw));
                $processedPrompt->setInput($readThis);
                $this->head->setNext(TuringMove::NEXT);
            }
            
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
        $blogArticle = BlogArticle::where('project_id', $this->project->id)->latest()->first();
        $fileMeta = json_decode($data);
        $file = Storage::disk($fileMeta->diskName)->path($fileMeta->fileName);

        $tmp1 = Storage::disk($fileMeta->diskName)->path($fileMeta->fileName . '.tmpfile1.mp3');
        $tmp2 = Storage::disk($fileMeta->diskName)->path($fileMeta->fileName . '.tmpfile2.mp3');

        $ffmpeg = FFMpeg::create();
        $audio = $ffmpeg->openAdvanced([$file]);
        $audio->filters()
              ->custom('[0:a]', 'dynaudnorm=f=100', '[a]');
        $audio
              ->map(array('[a]'), new \FFMpeg\Format\Audio\Mp3, $tmp1)
              ->save();

        $ffmpeg = FFMpeg::create();
        $audio = $ffmpeg->open($tmp1);
        $audio->filters()
              ->custom('equalizer=f=800:t=q:w=0.5:g=-15, equalizer=f=150:t=q:w=0.5:g=-30, aphaser=in_gain=0.8, volume=20dB');
        $audio->save(new \FFMpeg\Format\Audio\Mp3, $tmp2);

        $staticFile = app_path('Corporations/CreepyPastaMachine/resources/anomalous_interference.mp3');   

        if(empty($blogArticle->articleENvo))
        {
            $path = Storage::disk('public')->path('LaravelNeuro/CreepyPastaMachine/audio/article_'.$blogArticle->id.'_vo_en.mp3');
            
            $ffmpeg = FFMpeg::create();
            $audio = $ffmpeg->openAdvanced(array($tmp2, $staticFile));
            $audio->filters()
                  ->custom('[0:a][1:a]', 'amix=inputs=2:duration=first:dropout_transition=1', '[a]');
            $audio
                  ->map(array('[a]'), new \FFMpeg\Format\Audio\Mp3, $path)
                  ->save();

            Storage::disk($fileMeta->diskName)->delete($fileMeta->fileName . '.tmpfile1.mp3');
            Storage::disk($fileMeta->diskName)->delete($fileMeta->fileName . '.tmpfile2.mp3');
            Storage::disk($fileMeta->diskName)->delete($fileMeta->fileName);

            $blogArticle->articleENvo = $path;
            $blogArticle->save();
        }
        else
        {
            $path = Storage::disk('public')->path('LaravelNeuro/CreepyPastaMachine/audio/article_'.$blogArticle->id.'_vo_de.mp3');
            
            $ffmpeg = FFMpeg::create();
            $audio = $ffmpeg->openAdvanced(array($tmp2, $staticFile));
            $audio->filters()
                  ->custom('[0:a][1:a]', 'amix=inputs=2:duration=first:dropout_transition=1', '[a]');
            $audio
                  ->map(array('[a]'), new \FFMpeg\Format\Audio\Mp3, $path)
                  ->save();

            Storage::disk($fileMeta->diskName)->delete($fileMeta->fileName . '.tmpfile1.mp3');
            Storage::disk($fileMeta->diskName)->delete($fileMeta->fileName . '.tmpfile2.mp3');
            Storage::disk($fileMeta->diskName)->delete($fileMeta->fileName);

            $blogArticle->articleDEvo = $path;
            $blogArticle->save();
        }

        return $data;
    }
}