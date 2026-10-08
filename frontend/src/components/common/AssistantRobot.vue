<template>
  <svg
    :class="['robot', `mood-${mood}`, { 'head-only': headOnly }]"
    :viewBox="headOnly ? '30 0 140 125' : '0 0 200 220'"
    role="img"
    aria-label="Robot assistant"
  >
    <!-- Ground shadow -->
    <ellipse v-if="!headOnly" class="shadow" cx="100" cy="210" rx="42" ry="6" />

    <g class="bot">
      <!-- Arms (behind the body) -->
      <g v-if="!headOnly">
        <path class="arm" d="M64 134 Q42 152 46 176" />
        <!-- resting right arm, shown while listening / thinking -->
        <g class="arm-rest">
          <path class="arm" d="M136 134 Q158 152 154 176" />
          <circle class="hand" cx="154" cy="180" r="8" />
        </g>
        <g class="arm-wave">
          <path class="arm" d="M136 134 Q160 118 164 96" />
          <circle class="hand" cx="164" cy="92" r="8" />
        </g>
        <circle class="hand" cx="46" cy="180" r="8" />
      </g>

      <!-- Body -->
      <g v-if="!headOnly">
        <rect class="neck" x="88" y="112" width="24" height="14" rx="4" />
        <rect class="body" x="62" y="122" width="76" height="64" rx="20" />
        <circle class="core" cx="100" cy="152" r="10" />
        <rect class="belt" x="78" y="172" width="44" height="5" rx="2.5" />
      </g>

      <!-- Antenna -->
      <line class="antenna-stem" x1="100" y1="32" x2="100" y2="14" />
      <circle class="antenna-light" cx="100" cy="11" r="6" />

      <!-- Head -->
      <rect class="ear" x="37" y="60" width="10" height="26" rx="4" />
      <rect class="ear" x="153" y="60" width="10" height="26" rx="4" />
      <rect class="head" x="45" y="30" width="110" height="86" rx="28" />
      <rect class="screen" x="58" y="45" width="84" height="56" rx="18" />

      <g class="eyes">
        <rect class="eye" x="72" y="62" width="16" height="18" rx="8" />
        <rect class="eye" x="112" y="62" width="16" height="18" rx="8" />
      </g>
      <path class="mouth" d="M86 88 Q100 97 114 88" />
    </g>
  </svg>
</template>

<script setup>
defineProps({
  // idle: waves and waits · listening: looks down at the input · thinking: waiting for the answer
  mood: { type: String, default: 'idle' },
  headOnly: { type: Boolean, default: false },
})
</script>

<style scoped>
.robot { display: block; width: 100%; height: auto; overflow: visible; }
.robot * { transform-box: fill-box; }

.bot { animation: float 3.2s ease-in-out infinite; transform-origin: center; }
.shadow { fill: rgba(10, 36, 67, .14); transform-origin: center; animation: shadow 3.2s ease-in-out infinite; }

.head { fill: #0f3460; }
.screen { fill: #0a2443; }
.ear { fill: #0e7c7b; }
.neck { fill: #0a2443; }
.body { fill: #0f3460; }
.belt { fill: #0e7c7b; opacity: .7; }
.core { fill: #c9971c; transform-origin: center; animation: pulse 2s ease-in-out infinite; }
.arm { fill: none; stroke: #0f3460; stroke-width: 12; stroke-linecap: round; }
.hand { fill: #0e7c7b; }
.antenna-stem { stroke: #0f3460; stroke-width: 4; stroke-linecap: round; }
.antenna-light { fill: #c9971c; transform-origin: center; animation: pulse 1.6s ease-in-out infinite; }

.eyes { transition: transform .35s ease; }
.eye { fill: #5fe0d6; transform-origin: center; animation: blink 4.5s infinite; filter: drop-shadow(0 0 3px rgba(95, 224, 214, .8)); }
.mouth { fill: none; stroke: #5fe0d6; stroke-width: 4; stroke-linecap: round; transition: d .3s ease; }

.arm-wave { transform-origin: 0% 100%; animation: wave 2.4s ease-in-out infinite; }

/* listening: looks down at what you type, stops waving */
.mood-listening .eyes { transform: translateY(5px); }
.arm-rest { display: none; }
.mood-listening .arm-wave, .mood-thinking .arm-wave { display: none; }
.mood-listening .arm-rest, .mood-thinking .arm-rest { display: inline; }
.mood-listening .mouth { d: path('M90 90 Q100 94 110 90'); }

/* thinking: looks up, antenna blinks fast */
.mood-thinking .eyes { transform: translate(4px, -5px); }
.mood-thinking .antenna-light { animation-duration: .5s; }

.head-only .bot { animation: none; }

@keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
@keyframes shadow { 0%, 100% { transform: scaleX(1); opacity: 1; } 50% { transform: scaleX(.82); opacity: .6; } }
@keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: .55; transform: scale(.85); } }
@keyframes blink { 0%, 92%, 100% { transform: scaleY(1); } 95% { transform: scaleY(.1); } }
@keyframes wave { 0%, 100% { transform: rotate(0deg); } 25% { transform: rotate(-14deg); } 50% { transform: rotate(6deg); } 75% { transform: rotate(-10deg); } }

@media (prefers-reduced-motion: reduce) {
  .bot, .shadow, .core, .antenna-light, .eye, .arm-wave { animation: none; }
}
</style>
