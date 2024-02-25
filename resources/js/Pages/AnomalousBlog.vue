<template>
<v-container class="min-h-screen">
  
  <v-expansion-panels>
    <v-expansion-panel
      class="article_expander"
    >

    <a href="https://laravelneuro.org" target="_blank"><v-btn
            class="bg-slate-700 m-1"
            size="x-large"
          >
        <img class="max-h-9" :src="`${ application.assetURL }/images/LaravelNeuroIconTransparent.png`" alt="lneuroicon"/>
    </v-btn></a>

    <v-chip
            closable
            size="x-large"
            class="bg-slate-800 m-1"
            label
          >
          <div class="lang-slider"><div>🇬🇧</div><v-switch v-model="lang" class="lang-slider-control"></v-switch><div>🇩🇪</div></div>
    </v-chip>

    <v-btn
            class="bg-slate-700 m-1"
            size="large"
            @click="imprintOverlay = true"
          >
            {{ imprintBtn }}
    </v-btn>

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
            :article=article
            >
  <div class="nixie-wrapper" v-if="mobileHide"><NixieDate v-if="mobileHide" :date="article.created" /></div>
  <v-expansion-panels :mandatory="articles.length == 1 ? 'force' : false">
    <v-expansion-panel
      class="article_expander"
    >
    <v-expansion-panel-title class="article_expander_title_wrapper"
      @click="expandedTrigger"
    >
        <div v-html=article.img class="article_expander_img"></div>
        <div v-if="!mobileHide" class="mobileDate"><p>{{ dateMake(article.created) }}</p><hr/></div>
        <div class="article_expander_title">{{ article.title }}</div>
        <div class="scpDoNotReadContainer">
      <h1>{{ doNotRead }}</h1>
    </div>
      </v-expansion-panel-title>
    
      <v-expansion-panel-text class="article_expander_txt">
        <BlogArticle
          :article="article" role="article"
        />
      </v-expansion-panel-text>
      <VueSound :article-id=article.id :file=article.articleVo title="Article Voice-Over" class="article_expander_player"/>
      <ScpCard :scpData=article.SCPdata :scpClasses=article.SCPdataEN />
      <VueSound :scp-id=article.SCPdata.id :file=article.SCPdata.vo_file title="SCP Warning Voice-Over" class="article_expander_player"/>
    </v-expansion-panel>
  </v-expansion-panels>
  </div>
  <div class="w-full text-center">
    <v-btn
      v-if=showLoadMore
        class="bg-red-900 mt-4 w-full"
        size="x-large"
        @click=loadMore
      >
    {{ loadMoreBtn }}
    </v-btn>
  </div>
  
  </v-container>
  <v-overlay
          role="site_notice"
          v-model="imprintOverlay"
          contained
          class="align-center justify-center"
          scroll-strategy="close"
        >
        <v-card
        variant="elevated"
                class="bg-slate-800 text-gray-100 m-2"
                >
          <v-card-title class="text-slate-200 m-2">
              <v-icon start icon="mdi-developer-board"></v-icon>
              {{ imprintBtn }}
          </v-card-title>
          <v-card-text v-html="imprint" class="imprint m-4">
          </v-card-text>
        </v-card>
        <v-btn
            class="bg-slate-800 m-4"
            @click="imprintOverlay = false"
          >
            {{ imprintCloseBtn }}
          </v-btn>  
  </v-overlay>
  <v-overlay
          role="site_disclaimer"
          v-model="cookies"
          class="align-center justify-center"
          scroll-strategy="block"
          persistent
        >

        <v-card
        variant="elevated"
                class="bg-slate-800 text-gray-100 m-2"
                >
          <v-card-title class="text-slate-200 m-2">
              <v-icon start icon="mdi-developer-board"></v-icon>
              {{ disclaimerTitle }}
          </v-card-title>
          <v-chip
            closable
            size="x-large"
            class="bg-slate-800 ml-6"
            label
          >
          <div class="lang-slider"><div>🇬🇧</div><v-switch v-model="lang" class="lang-slider-control"></v-switch><div>🇩🇪</div></div>
          </v-chip>
          <v-card-text v-html="disclaimer" class="imprint m-4">
          </v-card-text>
        </v-card>
        <v-btn
            class="bg-slate-800 m-4"
            @click="cookieChoice('OK')"
          >
            OK          
        </v-btn> 
        <v-btn
            class="bg-slate-800 m-4"
            @click="cookieChoice('essential')"
          >
            {{ essentialBtn }}          
        </v-btn> 
  </v-overlay>
</template>

<script>
import { Link, Head, usePage, router } from '@inertiajs/vue3'
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
      shuffleBlock: false,
      doNotRead: 'WARNING: Do not read or play back article!',
      imprint: '<h3>Site Owner:</h3><img src="images/imprintadress.png"><h3>Site Notice:</h3><p>Any content on this site, even if based on real events, should be considered fictional, since it has been crafted and edited with the explicit goal of creating fictional content.</p><p>Furthermore, the majority of content on this page was created using generative AI models, which have been networked using the <a href="https://laravelneuro.org" target="_blank">LaravelNeuro</a> framework.</p>',
      imprintOverlay: false,
      imprintCloseBtn: 'Close Site Notice',
      imprintBtn: 'Site Notice',
      essentialBtn: 'Essential',
      loadMoreBtn: 'Load More',
      cookies: true,
      currentPage: page.props.currentPage,
      pages: page.props.pages,
      disclaimerTitle: 'Welcome to the Anomalous Blog.',
      disclaimer: '<p>This blog utilizes cookies for esstential site features, but also for analytics purposes via Google Tag Manager. If you would like to consent to analytics data of your visit being collected, please click "OK" below, or click "Essential" to only allow essential cookies.</p><br><div class="scpDoNotReadContainer"><h1>The SCP Foundation has seized control of this website to contain dangerous anomalies within. Due to unavoidable containment breaches, unauthorized users can sometimes still reach the Anomalous Blog. If you are not authorized, please leave this site immediately. Stay safe.</h1></div>',
      };
    },
  computed: {
    showLoadMore() { return this.currentPage < this.pages },
  },
  watch: {
    lang: function (val) {
      this.shuffleBlock = true;
      if(val)
      {
        this.setCookie('langPref', 'de', 7);
        this.articles = this.articlesData.de;
        this.topLevelWarning = this.topLevelWarningData.de;
        this.doNotRead = 'WARNUNG: Artikel unter keinen Umständen lesen oder abspielen!';
        this.imprint = '<h3>Seitenbesitzer:</h3><img src="images/imprintadress.png"><h3>Seitenhinweise:</h3><p>Jegliche Inhalte auf dieser Seite sollten als fiktiv behandelt werden. Auch wenn artikel auf echten Nachrichten und Artikeln basieren, wurden diese explizit mit erfundenen Inhalten geschrieben und bearbeitet und sind deswegen nicht als faktisch zu behandeln.</p><p>Zudem wurden die Artikel auf dieser Seite mithilfe von generativen KI-Modellen angefertigt, welche mit Hilfe des <a href="https://laravelneuro.org" target="_blank">LaravelNeuro</a> Frameworks vernetzt wurden.</p>';
        this.imprintBtn = 'Impressum';
        this.imprintCloseBtn = 'Impressum schließen';
        this.essentialBtn = 'Essenzielle';
        this.loadMoreBtn = 'Mehr Laden';
        this.disclaimerTitle = 'Willkommen zum Anomalous Blog.';
        this.disclaimer = '<p>Dieser Blog verwendet cookies für essenzielle-, aber auch für Analysezwecke mittels Google Tag Manager. Sollten Sie mit der Sammlung von Zugriffsdaten einverstanden sein, klicken Sie unten auf OK, oder klicken Sie auf "Essenzielle", um nur Cookies zu erlauben, die für den Betrieb dieser Seite notwendig sind.</p><br><div class="scpDoNotReadContainer"><h1>Die SCP Foundation hat die Kontrolle über diese Webpräsenz übernommen um gefährliche Anomalien einzudämmen. Aufgrund unvermeidbarer Eindämmungseinbrüche kann es dennoch passieren, dass unauthorisierte Nutzer diese Seite erreichen können. Sollten Sie nicht authorisiert sein, verlassen Sie den Anomalous Blog zu Ihrer eigenen Sicherheit bitte sofort. Seien Sie vorsichtig.</h1></div>';
      }
      else
      {
        this.setCookie('langPref', 'en', 7);
        this.articles = this.articlesData.en;
        this.topLevelWarning = this.topLevelWarningData.en;
        this.doNotRead = 'WARNING: Do not read or play back article!'
        this.imprint = '<h3>Site Owner:</h3><img src="images/imprintadress.png"><h3>Site Notice:</h3><p>Any content on this site, even if based on real events, should be considered fictional, since it has been crafted and edited with the explicit goal of creating fictional content.</p><p>Furthermore, the majority of content on this page was created using generative AI models, which have been networked using the <a href="https://laravelneuro.org" target="_blank">LaravelNeuro</a> framework.</p>';
        this.imprintBtn = 'Site Notice';
        this.imprintCloseBtn = 'Close Site Notice';
        this.essentialBtn = 'Essential';
        this.loadMoreBtn = 'Load More';
        this.disclaimerTitle = 'Welcome to the Anomalous Blog.';
        this.disclaimer = '<p>This blog utilizes cookies for esstential site features, but also for analytics purposes via Google Tag Manager. If you would like to consent to analytics data of your visit being collected, please click "OK" below, or click "Essential" to only allow essential cookies.</p><br><div class="scpDoNotReadContainer"><h1>The SCP Foundation has seized control of this website to contain dangerous anomalies within. Due to unavoidable containment breaches, unauthorized users can sometimes still reach the Anomalous Blog. If you are not authorized, please leave this site immediately. Stay safe.</h1></div>';
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
    cookieChoice(choice) {
      this.setCookie('cconsent', choice, 7);
      this.cookies = false;
      if(choice == 'OK')
      {
        this.$gtag.query('consent', 'update', {
          analytics_storage: 'granted',
          screen_name: 'Home page',
        })
      }
    },
    setCookie(cname, cvalue, exdays, sameSite = 'Lax') {
      const d = new Date();
      d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
      let expires = "expires=" + d.toUTCString();
      // Ensure sameSite value is one of 'None', 'Lax', or 'Strict'.
      sameSite = ['Lax', 'Strict', 'None'].includes(sameSite) ? sameSite : 'Lax';

      let cookieValue = cname + "=" + cvalue + ";" + expires + ";path=/;SameSite=" + sameSite;

      // If sameSite is 'None', also add the 'Secure' attribute.
      if (sameSite === 'None') {
        cookieValue += ";Secure";
      }

      document.cookie = cookieValue;
    },
    getCookie(cname) {
      let name = cname + "=";
      let decodedCookie = decodeURIComponent(document.cookie);
      let ca = decodedCookie.split(';');
      for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
          return c.substring(name.length, c.length);
        }
      }
      return "";
    },
    randomDate() {
      const startDate = new Date(1992, 0, 1); // January 1 of start year
      const endDate = new Date(7125, 11, 31); // December 31 of end year
      const dateRange = endDate - startDate;
      const randomTime = Math.random() * dateRange;
      const randomDate = new Date(startDate.getTime() + randomTime).toString();

      return randomDate;
    },
    shuffleNixies() {

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
          article.created = article.createdTmp;
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
    },
    loadMore()
    {
      axios.get(`/?page=${this.currentPage + 1}`)
        .then(response => {
          this.articlesData.en = this.articlesData.en.concat(response.data.articles.en);
          this.articlesData.de = this.articlesData.de.concat(response.data.articles.de);
          if(this.lang)
          {
            this.articles = this.articlesData.de;
          }
          else
          {
            this.articles = this.articlesData.en;
          }
          this.currentPage = this.currentPage + 1;
        })
        .catch(error => {
          console.error(error);
        });
    },
    expandedTrigger()
    {
      this.$gtag.event('articleExpanded');
    },
    audioTrigger()
    {
    },
  },
  mounted: function() {
    this.articles = this.articlesData.en;
    let langPref = this.getCookie('langPref');
    let cconsent = this.getCookie('cconsent');
    this.shuffleNixies();
    if(langPref == 'de')
    {
      this.lang = true;
    }
    if(cconsent == 'OK' || cconsent == 'essential')
    {
      this.cookies = false;
      if(cconsent == 'OK')
      {
        this.$gtag.query('consent', 'update', {
          analytics_storage: 'granted',
          screen_name: 'Home page',
        })
      }
    }
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
  
  .v-overlay--active {
    backdrop-filter: blur(2px);
    background: rgba(0, 0, 0, 0.8) !important;
  } 

  .v-overlay:deep(.v-overlay__scrim) {
    background: rgba(0, 0, 0, 1) !important;
  }

  .imprint {
    max-width: 800px;
  }

  .imprint:deep(h2, h3) {
    font-size: 150%;
    font-weight: 800;
    font-family: "Minion Pro Bold";
  }

  .imprint:deep(h3) { 
    font-size: 125%;
    margin: 10px 0;
  }

  .imprint:deep(p) { 
    font-size: 115%;
    margin: 5px 0;
  }

  .imprint:deep(a) { 
    color: orange;
    text-decoration: underline;
  }

  .scpDoNotReadContainer, .v-card-text:deep(.scpDoNotReadContainer) {
    padding: 1.5rem;
    width: 100%;
    background: repeating-linear-gradient(-45deg, #f2a417, #f2a417 15px, #141617 15px, #141617 30px);
  }
  .scpDoNotReadContainer > h1, .v-card-text:deep(.scpDoNotReadContainer > h1) {
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
      transition: all ease-in-out 1s;
    }

    .article_expander_img:hover {
      width: 500px;
      height: 500px;
      position: absolute;
      top: -80px;
      left: -80px;
      border-radius: 5px;
      overflow: hidden;
      border: 3px black solid;
      z-index: 999;
    }

    .article_expander_txt {
      padding: 5px 5px;
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