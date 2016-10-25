<template>
<div class="smart-date date no-wrap">
    <span :class="styleClass">{{ formattedDate }}</span>
</div>
</template>
<script>
export default {
    data(){
      return {
          parsedDate: ''
      }
    },
    computed: {
        today() {
            return moment();
        },
        tomorrow() {
            return this.today.clone().add(1, 'days').startOf('day');
        },
        yesterday() {
            return this.today.clone().subtract(1, 'days').startOf('day');
        },
        threeDaysAgo() {
            return this.today.clone().subtract(3, 'days').startOf('day');
        },
        nextSunday() {
          return this.today.clone().day(7).startOf('day');
        },
        formattedDate(){
            if (! this.date || this.date == '' || this.date === '0000-00-00 00:00:00') return;

                this.parsedDate = moment(this.date, ["DD/MM/YYYY", "YYYY-MM-DD HH:mm:ss"]);


                // TODO ::: Find a locale friendly way of doing this
                if(this.parsedDate.isSame(this.today, 'd')) return 'Today';
                if(this.parsedDate.isSame(this.yesterday, 'd')) return 'Yesterday';
                if(this.parsedDate.isSame(this.tomorrow, 'd')) return 'Tomorrow';

                // Within the week -> what day: Wednesday, Thursday, Friday ...
                if(this.today < this.parsedDate && this.parsedDate < this.nextSunday) return this.parsedDate.format('dddd');

                // Within the year -> month and date
                if(this.parsedDate.year() === this.today.year()) return this.parsedDate.format('MMM D');

                // Not this year -> month date, year
                return this.parsedDate.format('MMM D, YYYY');
        },
        styleClass() {
            if(! this.parsedDate) return;
            if(this.parsedDate.isBefore(this.threeDaysAgo, 'day')) return 'text-danger';
            if(this.parsedDate.isBefore(this.today, 'day')) return 'text-warning';
            if(this.parsedDate.isSame(this.today, 'd') || this.parsedDate.isSame(this.tomorrow, 'd')) return 'text-success';
        }
    },
    props: ['date'],
    methods: {

    }
}
</script>