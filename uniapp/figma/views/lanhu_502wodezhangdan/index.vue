<template>
  <div class="page flex-col">
    <div class="block_1 flex-col">
      <div class="section_1 flex-row">
        <span class="text_1">17:41</span>
        <div class="group_1 flex-col"></div>
        <div class="group_2 flex-col"></div>
        <img
          class="thumbnail_1"
          referrerpolicy="no-referrer"
          src="./assets/img/FigmaDDSSlicePNGd52eb7edc3332703a2debc88b3f73bbb.png"
        />
        <span class="text_2">82%</span>
      </div>
      <div class="section_2 flex-row">
        <div class="group_3 flex-col"></div>
        <span class="text_3">我的账单</span>
        <img
          class="image_1"
          referrerpolicy="no-referrer"
          src="./assets/img/FigmaDDSSlicePNGac40403b497a3e3c5b2e8df94903cc8b.png"
        />
      </div>
      <div class="section_3 flex-row">
        <div class="text-group_1 flex-col">
          <span class="text_4">我的账单</span>
          <span class="text_5">我的收入（元）</span>
          <span class="text_6">445.00</span>
        </div>
        <div class="box_1 flex-col justify-between">
          <span class="text_7">提现详情</span>
          <div class="text-group_2 flex-col justify-between">
            <span class="text_8">我的支出（元）</span>
            <span class="text_9">0.00</span>
          </div>
        </div>
        <img
          class="thumbnail_2"
          referrerpolicy="no-referrer"
          src="./assets/img/FigmaDDSSlicePNG2659c654b049d07761d2de7e1ae7fca4.png"
        />
      </div>
      <div class="text-wrapper_1 flex-col"><span class="text_10">立即提现</span></div>
      <div class="section_4 flex-row">
        <div class="section_5 flex-col"></div>
        <span class="text_11">收入详情</span>
        <span class="text_12">支出详情</span>
      </div>
      <div class="list_1 flex-col">
        <div class="list-items_1 flex-row justify-between" v-for="(item, index) in loopData0" :key="index">
          <div class="text-group_3 flex-col justify-between">
            <span class="text_13" v-html="item.lanhutext0"></span>
            <span class="text_14" v-html="item.lanhutext1"></span>
          </div>
          <div class="text-group_4 flex-col justify-between">
            <span class="text_15" v-html="item.lanhutext2"></span>
            <span class="text_16" :style="{ color: item.lanhufontColor3 }" v-html="item.lanhutext3"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      loopData0: [
        {
          lanhutext0: '小三数学家教2小时',
          lanhutext1: '2026.01.01&nbsp;23:00',
          lanhutext2: '+¥280.00',
          lanhutext3: '已入账',
          lanhufontColor3: 'rgba(153,153,153,1.000000)'
        },
        {
          lanhutext0: '整理衣柜需求',
          lanhutext1: '2026.01.01&nbsp;23:00',
          lanhutext2: '+¥130.00',
          lanhutext3: '待入账',
          lanhufontColor3: 'rgba(255,126,34,1.000000)'
        },
        {
          lanhutext0: '小三数学家教2小时',
          lanhutext1: '2026.01.01&nbsp;23:00',
          lanhutext2: '+¥280.00',
          lanhutext3: '已入账',
          lanhufontColor3: 'rgba(153,153,153,1.000000)'
        },
        {
          lanhutext0: '整理衣柜需求',
          lanhutext1: '2026.01.01&nbsp;23:00',
          lanhutext2: '+¥130.00',
          lanhutext3: '待入账',
          lanhufontColor3: 'rgba(255,126,34,1.000000)'
        }
      ],
      constants: {}
    };
  },
  methods: {}
};
</script>
<style scoped lang="scss" src="./assets/index.scss" />