<template>
    <div id="checklist-recipients">
        <div class="recipients" :class="{ expanded: expandRecipients }">
            <span class="span-to"><i class="fa fa-users"></i></span>
            <ul class="recipients-list list-unstyled list-inline">
                <li v-for="recipient in checklist.recipients">{{ recipient.email }}</li>
            </ul>
        </div>
        <div class="recipients recipients-sizer" :class="{ expanded: expandRecipients }">
            <span class="span-to"><i class="fa fa-users"></i></span>
            <ul class="recipients-list list-unstyled list-inline">
                <li v-for="recipient in checklist.recipients">{{ recipient.email }}</li>
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
                expandRecipients: false
            }
        },
        props: ['checklist'],
        methods: {
            toggleRecipientsCollapse() {
                this.expandRecipients = !this.expandRecipients;
            },
            setRecipientsCollapsability(element) {
                let containerWidth = $('#checklist-recipients').width();
                let contentWidth = $('.recipients-sizer').outerWidth() + 10;
                if (contentWidth > containerWidth) {
                    $(element).addClass('expandable');
                } else {
                    $(element).removeClass('expandable');
                }
            }
        },
        mounted() {
            // Sensor for recipients
            this.$nextTick(() => {
                let element = document.getElementById('checklist-recipients');
                this.setRecipientsCollapsability(element);
                new ResizeSensor(element, () => {
                    this.setRecipientsCollapsability(element);
                });
            })
        }
    }
</script>