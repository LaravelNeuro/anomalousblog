
<template>
    <div class="nixie-container">
      <span class="tube" v-for="(digit, index) in displayedDate" :key="index">
        <span class="digit" v-for="n in 10" :class="{ 'active': isActiveDigit(digit, n-1) }">{{ n - 1 }}</span>
      </span>
    </div>
    <div class="nixie-lables">
      <span class="lable-block">
        <span class="lable">D</span>
      </span>
      <span class="lable-block">
        <span class="lable">D</span>
      </span>
      <span class="lable-block">
        <span class="lable">M</span>
      </span>
      <span class="lable-block">
        <span class="lable">M</span>
      </span>
      <span class="lable-block">
        <span class="lable">Y</span>
      </span>
      <span class="lable-block">
        <span class="lable">Y</span>
      </span>
      <span class="lable-block">
        <span class="lable">Y</span>
      </span>
      <span class="lable-block">
        <span class="lable">Y</span>
      </span>
    </div>
</template>
    
  <script>
  export default {
    name: 'NixieDate',
    props: {
      date: {
        type: String,
        required: true,
      },
    },
    computed: {
      displayedDate() {
        const dateify = new Date(this.date);
        const year = dateify.getFullYear();
        const month = String(dateify.getMonth() + 1).padStart(2, '0');
        const day = String(dateify.getDate()).padStart(2, '0');
        return `${day}${month}${year}`;
      },
    },
    methods: {
      isActiveDigit(digit, n) {
        // Determine if the digit should be active based on the current number
        return parseInt(digit) === n;
      },
    },
    mounted() {
      
    },
  };
  </script>
  
  <style scoped>
  .nixie-lables {
    position: relative;
    font-family: "Minion Pro Bold";
    font-size: 1.0em; 
    text-align: right;
    margin-top: -30px;
    color: rgba(255, 255, 255, 0.5)
  }
  .nixie-container {
    position: relative;
    font-family: "Trebuchet";
    font-size: 3.5em; 
    text-align: right;
    letter-spacing: 0.1em;
  }
  .nixie-container .tube {
    position: relative;
    display: inline-block;
    text-align: center;
    width: 53px; 
    height: 83px; 
    margin: 0 2px 0 2px; 
    border-top: 1px solid rgba(240, 200, 200, 0.12); 
    border-left: 1px solid rgba(240, 200, 200, 0.1); 
    border-right: 1px solid rgba(240, 200, 200, 0.1); 
    border-bottom: 4px solid black; 
    border-radius: 21px 21px 3px 3px; 
    background-color: rgba(119, 72, 0, 0.662);
    box-shadow: inset -2px 1px 10px 1px rgba(255, 110, 11, 0.1), 0 -1px 11px 0 rgba(255, 170, 22, 0.2); 
    animation-duration: 4.5s;
    animation-name: tube-glow;
  }

  .nixie-lables .lable-block {
    display: inline-block;
    text-align: center;
    width: 53px; 
    height: 30px; 
    margin: 0px 2px 0 2px; 
    padding-top: 2px;
    border-top: 0; 
    border-left: 1px solid rgba(255, 255, 255, 0.2); ; 
    border-right: 1px solid rgba(255, 255, 255, 0.2); ; 
    border-bottom: 1px solid rgba(255, 255, 255, 0.2); ;  
    background-color: #1e1e1e;
  }

  .nixie-container .tube::before {
    content: "";
    position: absolute;
    top: 6px; 
    right: 5px; 
    width: 16px; 
    height: 8px; 
    border-top: 3.5px solid rgba(255, 255, 255, 0.05); 
    border-radius: 7px; 
    transform: rotate(43deg);
  }
  .nixie-container .tube::after {
    content: "";
    position: absolute;
    top: 28px; 
    right: 3.5px; 
    width: 3.5px; 
    height: 52.5px; 
    border-right: 1px solid rgba(255, 255, 255, 0.03); 
    border-radius: 2px; 
  }
  .nixie-container .tube .digit {
    position: absolute;
    width: 59.5px; 
    line-height: 1.65em;
    left: 0;
    text-shadow: rgba(50, 50, 50, 0.008) 0 0 1px;
    color: transparent;
    -webkit-text-stroke-width: 1px; 
    -webkit-text-stroke-color: rgba(40, 40, 40, 0.08);
  }
  .nixie-container .tube .digit.active {
    color: #ffa600;
    opacity: 1;
    text-shadow: #c83d01 0 0 39px, #ffa200 0 0 17px, #ffd58c 0 0 8px, #ffdb9d 0 0 5.6px, #fffaf2 0 0 1.4px; 
    -webkit-text-stroke-width: 1px; 
    -webkit-text-stroke-color: hsla(21, 100%, 55%, 0.804);
  }

  @keyframes tube-glow {
  0% {
    box-shadow: inset -3px 4px 30px 2px rgba(255, 110, 11, 0.07), 0 -2px 32px 0 rgba(255, 169, 22, 0.06);
    border-top: 2px solid rgba(240, 150, 150, 0.15);
    border-right: 2px solid rgba(240, 150, 150, 0.15);
  }
  30% {
    box-shadow: inset -6px 4px 30px 2px rgba(255, 110, 11, 0.1), 0 -2px 32px 0 rgba(255, 169, 22, 0.06);
  }
  55% {
    box-shadow: inset -5px 4px 30px 2px rgba(255, 110, 11, 0.08), 0 -2px 32px 0 rgba(255, 169, 22, 0.06);
  }
  70% {
    box-shadow: inset -6px 4px 30px 2px rgba(255, 110, 11, 0.09), 0 -2px 32px 0 rgba(255, 169, 22, 0.06);
  }
  78% {
    box-shadow: inset -4px 4px 30px 2px rgba(255, 110, 11, 0.02), 0 -2px 32px 0 rgba(255, 169, 22, 0.06);
    border-top: 2px solid rgba(240, 150, 150, 0.12);
    border-right: 2px solid rgba(240, 150, 150, 0.12);
  }
  85% {
    box-shadow: inset -2px 4px 30px 2px rgba(255, 110, 11, 0.02), 0 -2px 32px 0 rgba(255, 169, 22, 0.06);
  }
  90% {
    box-shadow: inset 0 4px 30px 2px rgba(255, 110, 11, 0.09), 0 -2px 32px 0 rgba(255, 169, 22, 0.06);
  }
  96% {
    box-shadow: inset -1px 4px 30px 2px rgba(255, 110, 11, 0.01), 0 -2px 32px 0 rgba(255, 169, 22, 0.06);
  }
}
</style>