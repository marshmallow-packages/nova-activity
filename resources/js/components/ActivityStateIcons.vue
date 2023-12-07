<template>
    <div class="tw-mr-2" v-if="has_state_icons">
        <div class="tw-flex tw-gap-x-1">
            <div v-if="comment.is_pinned" class="tw-text-red-500">
                <Icon
                    :width="16"
                    type="exclamation"
                    :solid="true"
                />
            </div>
            <div v-if="comment.is_hidden" class="tw-text-gray-300">
                <Icon
                    :width="16"
                    type="eye-off"
                    :solid="true"
                />
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["resourceName", "resourceId", "field", "comment"],

        data() {
            return {
                has_state_icons: false,
            };
        },

        created() {
            if (this.comment.is_pinned || this.comment.is_hidden) {
                this.has_state_icons = true;
            }
        },

        methods: {

        },
    };
</script>
