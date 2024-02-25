<template>
    <v-card  outlined>
      <v-card-text class="article" v-html="article.article">
      </v-card-text>
      <v-card-text class="assessment">
        <h1>
          SCP Threat Assessment Report
        </h1>
        <div v-html=article.SCPdata.assessment>
          
        </div>
      </v-card-text>
    </v-card>
</template>
  
  <script>
  export default {
    name: 'BlogArticle',
    props: {
      article: {
        type: Object,
        required: true,
      },
    },
  };
  </script>
  
  <style scoped>
    .article:deep(img) {
        max-width: 100%;
    }

    .article:deep(img) {
        max-width: 100%;
    }

    .article {
        font-size: 120%;
        padding: 10px 10px;
        color: #00a700;
    }
    .article:deep(p) {
    line-height: 1.2;
    }

    .article:deep(h1) {
      font-size: 150%;
      font-weight: 800;
      margin: 20px 0 10px 0;
      text-decoration: double;
    }

    .article:deep(h2) {
      font-size: 130%;
      font-weight: 800;
      margin: 15px 0 10px 0;
      text-decoration: underline;
    }

    .article:deep(h3) {
      font-size: 115%;
      font-weight: 800;
      margin: 10px 0 5px 0;
    }

    .article:deep(b) {
    font-weight: 800;
    }

    .assessment {
      background-color: rgb(190, 107, 107);
      color: black;
      padding: 5px 5px;
      border: 5px solid rgb(153, 13, 13);
    }

    .assessment h1 {
      font-size: 115%;
      font-weight: 800;
    }

    @media screen and (min-width: 800px) {
      .article {
        font-size: 120%;
        padding: 20px 75px;
        color: #00a700;
      }
      .article:deep(p) {
      line-height: 1.5;
      }

      .assessment {
        background-color: rgb(190, 107, 107);
        color: black;
        margin: 10px 10px;
        padding: 30px 30px;
        border: 5px solid rgb(153, 13, 13);
      }

      .assessment h1 {
        font-size: 125%;
        font-weight: 800;
      }
    }

  </style>