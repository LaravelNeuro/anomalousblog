<template>
<v-container class="min-h-screen">
  
  <v-expansion-panels>
    <v-expansion-panel
      class="article_expander"
    >
    <v-chip
            closable
            style="height: 50px;"
            class="bg-slate-800"
            label
          >
          <div class="lang-slider"><div>🇬🇧</div><v-switch v-model="lang" class="lang-slider-control"></v-switch><div>🇩🇪</div></div>
        </v-chip>
    <v-expansion-panel-title class="article_expander_title_wrapper">
        <div class="article_expander_title">{{ topLevelWarning.title }}</div>
    </v-expansion-panel-title>
      <v-expansion-panel-text class="">
        <div v-html="topLevelWarning.assessment" class="tlWarning">

        </div>
      </v-expansion-panel-text>
      <VueSound :scp-id=1 :file=topLevelWarning.vo_file title="SCP Warning Voice-Over" class="article_expander_player"/>
      <ScpCard :scpData="topLevelWarning" :scpClasses="topLevelWarningData.en" />
    </v-expansion-panel>
  </v-expansion-panels> 
  <div  class="article_wrapper"
        v-for="article in articles"
            :key=article.id
            :article=article>
  <div class="nixie-wrapper" v-if="mobileHide"><NixieDate v-if="mobileHide" :date="article.created" /></div>
  <v-expansion-panels>
    <v-expansion-panel
      class="article_expander"
    >
    <v-expansion-panel-title class="article_expander_title_wrapper">
        <div v-html=article.img class="article_expander_img"></div>
        <div v-if="!mobileHide" class="mobileDate"><p>{{ dateMake(article.created) }}</p><hr/></div>
        <div class="article_expander_title">{{ article.title }}</div>
        <div class="scpDoNotReadContainer">
      <h1>{{ doNotRead }}</h1>
    </div>
      </v-expansion-panel-title>
    
      <v-expansion-panel-text class="article_expander_txt">
        <BlogArticle
          :article="article"
        />
      </v-expansion-panel-text>
      <VueSound :article-id=article.id :file=article.articleVo title="Article Voice-Over" class="article_expander_player"/>
      <ScpCard :scpData=article.SCPdata :scpClasses=article.SCPdataEN />
      <VueSound :scp-id=article.SCPdata.id :file=article.SCPdata.vo_file title="SCP Warning Voice-Over" class="article_expander_player"/>
    </v-expansion-panel>
  </v-expansion-panels>
  </div>
  </v-container>
</template>

<script>
import { Link, Head, usePage } from '@inertiajs/vue3'
import BlogArticle from './../Components/BlogArticle.vue'
import ScpCard from './../Components/ScpCard.vue'
import NixieDate from './../Components/NixieDate.vue'
import { VueSound } from 'vue-sound'

const page = usePage();

export default {
  name: 'BlogPage',
  components: {
        Link, 
        Head,
        BlogArticle,
        ScpCard,
        NixieDate,
        VueSound,
    },
  data() {
    return {
      articlesData: page.props.articles,
      articles: null,
      topLevelWarningData: page.props.topLevelWarning,
      topLevelWarning: page.props.topLevelWarning.en,
      application: page.props.application,
      mobileHide: true,
      window: {
            width: 0,
            height: 0
        },
      lang: false,
      doNotRead: 'WARNING: Do not read or playback article!'
      };
    },
  watch: {
    lang: function (val) {
      if(val)
      {
        this.articles = this.articlesData.de;
        this.topLevelWarning = this.topLevelWarningData.de;
        this.doNotRead = 'WARNUNG: Artikel unter keinen Umständen lesen oder abspielen!';
      }
      else
      {
        this.articles = this.articlesData.en;
        this.topLevelWarning = this.topLevelWarningData.en;
        this.doNotRead = 'WARNING: Do not read or playback article!'
      }
    },
  },
    created() {
        window.addEventListener('resize', this.handleResize);
        this.handleResize();
    },
    destroyed() {
        window.removeEventListener('resize', this.handleResize);
    },
  methods: {
    randomDate() {
      const startDate = new Date(1992, 0, 1); // January 1 of start year
      const endDate = new Date(7125, 11, 31); // December 31 of end year
      const dateRange = endDate - startDate;
      const randomTime = Math.random() * dateRange;
      const randomDate = new Date(startDate.getTime() + randomTime).toString();

      return randomDate;
    },
    shuffleNixies() {
      
      this.articles = this.articles.map(article => {
        article.realDate = article.created;
        return { ...article };
      });

      for(var i = 0; i < 20; i++)
      {
        setTimeout(() => {
        this.articles = this.articles.map(article => {
        article.created = this.randomDate();
        return { ...article };
        });}, i*50);
      }

      setTimeout(() => {
        this.articles = this.articles.map(article => {
        article.created = article.realDate;
        return { ...article };
      });}, 1000);

      setTimeout(this.shuffleNixies, Math.random() * 60000 + 1200);

    },
    handleResize() {
            this.window.width = window.innerWidth;
            this.window.height = window.innerHeight;
            
            if(this.window.width < 800)
            {
              this.mobileHide = false;
            }
            else
            {
              this.mobileHide = true;
            }
        },
    dateMake(date)
    {
      const dateify = new Date(date);
      const year = dateify.getFullYear();
      const month = String(dateify.getMonth() + 1).padStart(2, '0');
      const day = String(dateify.getDate()).padStart(2, '0');
      return `${day}.${month}.${year}`;
    }
  },
  mounted: function() {
    this.articles = this.articlesData.en;
    this.shuffleNixies();
  }
}
</script>

<style>
@import '../../../node_modules/vue-sound/dist/style.css';
  :root {
    --player-background: var(--v-theme-background);
    --player-font-family: "Open Sans", sans-serif;
    --player-font-size: 0.9rem;
    --player-font-size-small: 0.7rem;
    --player-font-weight: 300;
    --player-font-weight-bold: 600;
    --player-text-color: #ffffff;
    --player-icon-color: var(--player-text-color);
    --player-link-color: var(--player-text-color);
    --player-progress-color: #41b883;
    --player-buffered-color: #41b883;
    --player-seeker-color: #ffffff;
    --player-input-range-color: var(--player-text-color);
  }
</style>

<style scoped>
  .lang-slider {
    display: flex;
    flex-direction: row;
    align-items: center;
    margin-top: 20px;
  }
  .lang-slider div {
    margin: 0 5px 15px 5px;
    font-size: 150%;
  }
  .lang-slider-control {
    max-height: 60px;
  }
  .tlWarning {
    line-height: 1.5;
    font-size: 110%;
    color: gainsboro;
  }
  .tlWarning:deep(.threatAssessment){
    background: rgba(255, 255, 0, 0.1);
    padding: 10px 10px;
  }
  .tlWarning:deep(.threatMeasures){
    background: rgba(9, 255, 0, 0.1);
    padding: 10px 10px;
  }
  .tlWarning:deep(.threatWarning){
    background: rgba(255, 16, 16, 0.1);
    padding: 10px 10px;
  }
  .tlWarning:deep(h3) {
    font-size: 125%;
    font-weight: 800;
  }
  .tlWarning:deep(hr) {
    margin: 5px 0;
  }

  .scpDoNotReadContainer {
    padding: 1.5rem;
    width: 100%;
    background: repeating-linear-gradient(-45deg, #f2a417, #f2a417 15px, #141617 15px, #141617 30px);
  }
  .scpDoNotReadContainer:deep(h1) {
    font-size: 140%;
    font-weight: 800;
    font-family: "Minion Pro Bold";
    background-color: #f2a417;
    border: 3px solid black;
    padding: 5px 5px;
    text-align: center;
    color: black;
  }
  .article_expander_txt:deep(> div) {
                  padding: 5px 5px;
             }
  .nixie-wrapper {
    width: 100%;
    margin-left: 42px;
  }

  .article_wrapper {
      margin-top: 60px;
    }

  .article_expander_title {
      margin: 5px 5px;
      font-size: 125%;
      line-height: 1.1;
      font-weight: 800;
    }
    .article_expander_img img{
      width: 75px;
      height: 75px;
      border: 2px gray ridge;
      overflow: hidden;
    }
    .mobileDate {
      width: 100%;
      text-align: center;
      padding: 10px 10px 0 10px;
      font-family: "Minion Pro Bold";
      font-size: 115%;
    }

    .mobileDate hr{
      border-color: gray;
    }

    .article_expander_title_wrapper {
      display: flex;
      flex-direction: column;
    }
    .article_expander_title {
      margin-top: 5px;
    }

  @media screen and (min-width: 800px) {
    .article_expander {
    margin-left: 80px;
    }
    .article_expander_title_wrapper {
      display: default;
    }
    .article_expander_title {
      margin: 5px 5px;
    }
    .article_wrapper:first-child {
      margin-top: 80px;
    }
    .article_wrapper:not(:first-child) {
      margin-top: 120px;
    }
    .article_expander_title {
      margin-left: 80px;
      font-size: 150%;
      line-height: 1.1;
      font-weight: 800;
    }
    .article_expander_img {
      width: 150px;
      height: 150px;
      position: absolute;
      top: -80px;
      left: -80px;
      border: 2px gray ridge;
      border-radius: 100%;
      overflow: hidden;
    }
    .article_expander_txt {
      padding: 0 0;
      margin-left: -80px;
    }
    .article_expander_img img {
      max-width: 100%;
      max-height: 100%;
    }
  }

  .v-expansion-panel-text .v-card{
    background: rgb(24, 24, 24);
  }

</style>