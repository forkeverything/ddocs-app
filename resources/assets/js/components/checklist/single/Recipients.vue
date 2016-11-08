<template>
    <div class="checklist-recipients"
         :class="{
            expandable: expandable
         }"
         ref="container"
    >
        <div class="recipients" :class="{ expanded: expandRecipients }">
            <span class="span-to"><i class="fa fa-users"></i></span>
            <ul class="recipients-list list-unstyled list-inline">
                <li v-for="recipient in recipients">{{ recipient.email }}</li>
            </ul>
        </div>
        <div class="recipients recipients-sizer" :class="{ expanded: expandRecipients }" ref="sizer">
            <span class="span-to"><i class="fa fa-users"></i></span>
            <ul class="recipients-list list-unstyled list-inline">
                <li v-for="recipient in recipients">{{ recipient.email }}</li>
            </ul>
        </div>
        <div class="expander">
            <span @click="toggleRecipientsCollapse">
            <i v-show="! expandRecipients" class="fa fa-angle-double-down"></i>
            <i v-show="expandRecipients" class="fa fa-angle-double-up"></i>
            </span>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                expandable: false,
                expandRecipients: false
            }
        },
        props: [],
        computed: {
            recipients() {
                return this.$store.state.checklist.data.recipients;
            },
            overflowing() {
                let containerWidth = $('#checklist-recipients').width();
                let contentWidth = $('.recipients-sizer').outerWidth() + 10;
                return contentWidth > containerWidth;
            }
        },
        methods: {
            toggleRecipientsCollapse() {
                this.expandRecipients = !this.expandRecipients;
            },
            setRecipientsCollapsability() {
                let containerWidth = this.$refs.container.offsetWidth;
                let contentWidth = this.$refs.sizer.offsetWidth + 10;
                this.expandable = contentWidth > containerWidth;
            }
        },
        created() {
            window.addEventListener('resize', this.setRecipientsCollapsability)
        },
        mounted() {
            this.$nextTick(this.setRecipientsCollapsability);
        },
        beforeDestroy: function () {
            window.removeEventListener('resize', this.setRecipientsCollapsability)
        }
    }
</script>