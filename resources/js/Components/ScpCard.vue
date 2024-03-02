<template>
    <div class="scp-wrapper">
        <div class="scp-header">
            <div class="scp-header-left">
                <h1>!SCP Foundation Warning!</h1>
                <h3>ITEM#: SCP-anomalous-blog-{{ scpData.blog_id }}</h3>
            </div>
            <div class="scp-header-right">
                <h2>{{ scpData.clearance.level.toUpperCase() }}</h2>
                <h3>{{ scpData.clearance.name.toUpperCase() }}</h3>
            </div>
        </div>

        <div class="scp-special-warning" v-if=specialWarning v-html="scpData.specialWarning">

        </div>

        <div class="scp-classification">
            <div :class="`scp-flex-row scp-threat-${ scpClasses.threat }-bgt scp-threat-${ scpClasses.threat }-bl`">
                <div class="scp-class">
                    <div class="scp-text">
                        <h3>CONTAINMENT CLASS:</h3>
                        <h1>{{ scpData.containment.toUpperCase() }}</h1>
                        <h4 :class="`scp-threat-${ scpClasses.threat }-bgf scp-tl`">TL:{{ scpData.threat.toUpperCase() }}</h4>
                    </div>
                </div>

                <div class="scp-lock">
                    <!-- Lock icon image -->
                    <img :class="`scp-threat-${ scpClasses.threat }-bgf`" :src="`${application.assetURL}storage/icons/classification/${scpClasses.containment}-icon.svg`" alt="Lock Icon" />
                </div>
            </div>
            
            <div class="scp-flex-column">
                <div :class="`scp-disruption scp-disruption-${ scpClasses.disruption.name }-bgt scp-disruption-${ scpData.disruption.name }-bl`">
                    <div class="scp-text scp-flex-bottom">
                        <h3>{{ scpData.disruption.name.toUpperCase() }}</h3>
                        <p><small>DISRUPTION CLASS:</small></p> 
                    </div>
                    <div class="scp-disruption-icon">
                        <div class="scp-icon-wrapper">
                            <span class="scp-icon-number">{{ scpData.disruption.level }}</span><img :class="`scp-disruption-${ scpClasses.disruption.name }-bgf`" :src="`${application.assetURL}storage/icons/disruption/${ scpClasses.disruption.name }-icon.svg`" alt="Disruption Icon" />
                        </div>
                        
                    </div>
                </div>
                <div :class="`scp-risk scp-risk-${ scpClasses.risk.name }-bgt scp-risk-${ scpClasses.risk.name }-bl`" style="margin-top: 5px;">
                    <div class="scp-text scp-flex-bottom">
                        <h3>{{ scpData.risk.name.toUpperCase() }}</h3>
                        <p><small>RISK CLASS:</small></p> 
                    </div>
                    <div class="scp-risk-icon">
                        <div class="scp-icon-wrapper">
                            <span class="scp-icon-number">{{ scpClasses.risk.level }}</span><img :class="`scp-risk-${ scpClasses.risk.name }-bgf`" :src="`${application.assetURL}storage/icons/risk/${ scpClasses.risk.name }-icon.svg`" :alt="`${ scpData.risk.name } Icon`" />
                        </div>                    
                    </div>
                </div>
            </div>

            <div style="margin: auto auto;">
                <img style="filter:invert();" class="scp-logo" :src="`${application.assetURL}storage/icons/SCP-Foundation-logo.svg`" alt="SCP Icon" />
            </div>

        </div>
    </div>
</template>
  
  <script>
  import { usePage } from '@inertiajs/vue3'

  const page = usePage();
  export default {
    name: 'ScpCard',
    props: {
        scpData: {
            type: Object,
            required: true,
        },
        scpClasses: {
            type: Object,
            required: true,
        },
    },
    computed: {
        specialWarning(){
        return Object.hasOwn(this.scpData, 'specialWarning');
        }
    },
    data() {
    return {
      application: page.props.application
      };
    },
  };
  </script>
  
  <style scoped>

  h1 {
    font-size: 120%;
    font-weight: 800;
  }

  .scp-wrapper {
      background-color: rgb(190, 107, 107);
      color: rgb(235, 235, 235);
  }

  .scp-header {
      display: flex;
      justify-content: space-between;
      align-items: stretch;
      padding: 10px;
      color: black;
      border: 5px solid rgb(153, 13, 13);
  }

  .scp-special-warning {
    border-left: 5px solid rgb(153, 13, 13);
    border-right: 5px solid rgb(153, 13, 13);
    border-bottom: 5px solid rgb(153, 13, 13);
    padding: 10px;
    color: black;
  }

  .scp-special-warning:deep(h3) {
    font-size: 120%;
    font-weight: 800;
  }

  .scp-header-right > * {
      margin: 0;
      padding: 0;
  }

  .scp-flex-bottom {
      display: flex;
      align-items: bottom;
      flex-direction: column-reverse;
  }

  .scp-flex-column {
      display: flex;
      align-items: bottom;
      flex-direction: column;
      flex-grow: 1;
      margin-left: 5px;
  }

  .scp-flex-row {
      display: flex;
      align-items: center;
      flex-direction: row;
      flex-grow: 1;
  }

  .scp-header div,
  .scp-header h1 {
      margin: 0;
  }

  .scp-classification {
        display: flex;
        justify-content: space-between;
        background-color: rgb(0, 0, 0);
        border: 0;
        border-top: 8px solid #000;
        padding: 10px;
        flex-direction: column;
    }
  
  .scp-classification div {
        text-align: left;
    }

  .scp-disruption {
    margin-top: 5px;
  }

  .scp-header-right {
    margin-left: 5px !important;
  }

  @media screen and (min-width: 800px) {
    .scp-header-right {
    margin-left: 0  !important;
    }
    .scp-disruption {
    margin-top: 0;
     }
    .scp-classification {
        flex-direction: row;
    }

  }
  .scp-classification .scp-class h1, .scp-classification .scp-class h3 {
      margin: 0;
      padding: 0;
  }

  .scp-classification .scp-lock {
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: center;
  }

  .scp-classification .scp-logo {
      width: 115px;
      margin: auto auto;
      flex-grow: 1;
      flex-shrink: 1;
  }
  

  .scp-classification .scp-class,
  .scp-classification .scp-disruption,
  .scp-classification .scp-risk {
      padding-left: 5px;
      padding-bottom: 5px;
      padding-right: 3px;
      padding-top:3px;
      display: flex;
      flex-direction: row;
      align-items: stretch;
      flex-grow: 1;
  }

  .scp-classification .scp-disruption > .scp-text,
  .scp-classification .scp-risk > .scp-text {
  margin: 0 auto 0 0;
  }

  .scp-classification .scp-disruption > .scp-text > *,
  .scp-classification .scp-risk > .scp-text > * {
      text-align: left; 
      margin: 0; 
      padding: 0;
  }

  .scp-lock img {
      width: 75px;
      border-radius: 100% !important;
      border: 3px solid black;
  }

  .scp-classification .scp-disruption img,
  .scp-classification .scp-risk img {
      width: 50px;
      border-radius: 100% !important;
  }

  .scp-classification .scp-icon-wrapper {
      padding: 5px 5px;
      background: black;
      border-radius: 40px;
      margin-right: 0;
      margin-bottom: 0;
      margin-top: 3px;
      margin-left: 10px;
      display: flex;
      flex-direction: row;
  }

  .scp-classification .scp-icon-wrapper span{
      margin-right: 10px;
      margin-top: auto;
      margin-bottom: auto;
      margin-left: 5px;
      font-size: 130%;
      font-weight: 800;
      color: rgb(235, 235, 235);
  }

  .scp-classification .scp-tl {
      padding: 3px 15px;
      text-align: right;
      color: rgb(235, 235, 235);
      border-radius: 0 5px 5px 0;
      margin: 0;
      margin-left: -5px !important;
      text-shadow: rgb(0, 0, 0) 1px 0px 0px, rgb(0, 0, 0) 0.540302px 0.841471px 0px, rgb(0, 0, 0) -0.416147px 0.909297px 0px, rgb(0, 0, 0) -0.989993px 0.14112px 0px, rgb(0, 0, 0) -0.653644px -0.756803px 0px, rgb(0, 0, 0) 0.283662px -0.958924px 0px, rgb(0, 0, 0) 0.96017px -0.279416px 0px;
  }

  .scp-description {
      width: 90%;
      font-size: 110%;
      line-height: 2em;
      margin: 0 auto 10px auto;
      border-bottom: 5px solid black;
  }

  .scp-fileImage img{
      max-width: 100%;
  }

  .scp-filetts {
      font-size:125%;
      border-width:1px;
      color:#fff;
      border-color:#314179;
      border-top-left-radius:0px;
      border-top-right-radius:0px;
      border-bottom-left-radius:0px;
      border-bottom-right-radius:0px;
      box-shadow:inset 0px 1px 0px 0px #7a8eb9;
      padding: 2px 2px;
      background:#a4c1ff;
      cursor: pointer;
  }

  .scp-filetts:hover {
      background: #5c88e6;
  }

  .scp-threat-White-bgt {
      background-color: #8a8a8a;
  }
      .scp-threat-White-bgf {
          background-color: #8a8a8a;
      }
      .scp-threat-White-bl {
          border-left: 10px solid #fff;
      }

  .scp-threat-Blue-bgt {
      background-color: #0088ff50;
  }
      .scp-threat-Blue-bgf {
          background-color: #0088ff;
      }
      .scp-threat-Blue-bl {
          border-left: 10px solid #0088ff;
      }
  
  .scp-threat-Green-bgt {
      background-color: #00770050;
  }
      .scp-threat-Green-bgf {
          background-color: #007700;
      }
      .scp-threat-Green-bl {
          border-left: 10px solid #007700;
      }

  .scp-threat-Yellow-bgt {
      background-color: #cccc0050;
  }
      .scp-threat-Yellow-bgf {
          background-color: #cccc00;
      }
      .scp-threat-Yellow-bl {
          border-left: 10px solid #cccc00;
      }  
      
  .scp-threat-Orange-bgt {
      background-color: #e0641c50;
  }
      .scp-threat-Orange-bgf {
          background-color: #e0641c;
      }
      .scp-threat-Orange-bl {
          border-left: 10px solid #e0641c;
      }         
      
  .scp-threat-Red-bgt {
      background-color: #9c1e1e50;
  }
      .scp-threat-Red-bgf {
          background-color: #9c1e1e;
      }
      .scp-threat-Red-bl {
          border-left: 10px solid #9c1e1e;
      }     
      
  .scp-threat-Black-bgt {
      background-color: #00000050;
  }
      .scp-threat-Black-bgf {
          background-color: #222;
      }
      .scp-threat-Black-bl {
          border-left: 10px solid #222;
      }     

  .scp-disruption-Dark-bgt {
      background-color: #00770050;
  }
      .scp-disruption-Dark-bgf {
          background-color: #007700;
      }
      .scp-disruption-Dark-bl {
          border-left: 10px solid #007700;
      }

  .scp-disruption-Vlam-bgt {
      background-color: #0087bd50;
  }
      .scp-disruption-Vlam-bgf {
          background-color: #0087bd;
      }
      .scp-disruption-Vlam-bl {
          border-left: 10px solid #0087bd;
      }
      
  .scp-disruption-Keneq-bgt {
      background-color: #cccc0050;
  }
      .scp-disruption-Keneq-bgf {
          background-color: #cccc00;
      }
      .scp-disruption-Keneq-bl {
          border-left: 10px solid #cccc00;
      }
      
  .scp-disruption-Ekhi-bgt {
      background-color: #e0641c50;
  }
      .scp-disruption-Ekhi-bgf {
          background-color: #e0641c;
      }
      .scp-disruption-Ekhi-bl {
          border-left: 10px solid #e0641c;
      }

  .scp-disruption-Amida-bgt {
      background-color: #9c1e1e50;
  }
      .scp-disruption-Amida-bgf {
          background-color: #9c1e1e;
      }
      .scp-disruption-Amida-bl {
          border-left: 10px solid #9c1e1e;
      }


  .scp-risk-Notice-bgt {
      background-color: #009f6b50;
  }
      .scp-risk-Notice-bgf {
          background-color: #009f6b;
      }
      .scp-risk-Notice-bl {
          border-left: 10px solid #009f6b;
      }

  .scp-risk-Caution-bgt {
      background-color: #0087bd50;
  }
      .scp-risk-Caution-bgf {
          background-color: #0087bd;
      }
      .scp-risk-Caution-bl {
          border-left: 10px solid #0087bd;
      }
      
  .scp-risk-Warning-bgt {
      background-color: #cccc0050;
  }
      .scp-risk-Warning-bgf {
          background-color: #cccc00;
      }
      .scp-risk-Warning-bl {
          border-left: 10px solid #cccc00;
      }
      
  .scp-risk-Danger-bgt {
      background-color: #e0641c50;
  }
      .scp-risk-Danger-bgf {
          background-color: #e0641c;
      }
      .scp-risk-Danger-bl {
          border-left: 10px solid #e0641c;
      }

  .scp-risk-Critical-bgt {
      background-color: #9c1e1e50;
  }
      .scp-risk-Critical-bgf {
          background-color: #9c1e1e;
      }
      .scp-risk-Critical-bl {
          border-left: 10px solid #9c1e1e;
      }

</style>