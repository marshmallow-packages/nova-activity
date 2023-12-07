<template>
    <div
        v-if="comment_history && comment_history.length"
        class="tw-mb-8 tw-mt-2"
    >
        <h2
            v-if="field.activity_title"
            class="tw-text-sm tw-font-semibold tw-leading-6 tw-text-gray-900 tw-mb-6"
        >
            {{ field.activity_title }}
        </h2>
        <ul role="list" class="tw-space-y-6">
            <template v-for="comment_history_item in comment_history">
                <HistoryWithOutComment
                    v-bind:key="comment_history_item.id"
                    v-if="
                        !comment_history_item.comment ||
                        comment_history_item.is_hidden
                    "
                    :comment="comment_history_item"
                    :resourceName="resourceName"
                    :resourceId="resourceId"
                    :field="field"
                />

                <HistoryWithComment
                    v-bind:key="comment_history_item.id"
                    v-if="comment_history_item.comment && !comment_history_item.is_hidden"
                    :comment="comment_history_item"
                    :resourceName="resourceName"
                    :resourceId="resourceId"
                    :field="field"
                />
            </template>
        </ul>
    </div>
</template>

<script>
    import HistoryWithComment from "./HistoryWithComment";
    import HistoryWithOutComment from "./HistoryWithOutComment";

    export default {
        props: ["resourceName", "resourceId", "field"],

        components: { HistoryWithComment, HistoryWithOutComment },

        data() {
            return {
                comment_history: [],
            };
        },

        created() {
            this.loadCommentHistory();
        },

        methods: {
            loadCommentHistory() {
                let self = this;
                Nova.request()
                    .get(
                        `/nova-vendor/nova-comments/${this.resourceName}/${this.resourceId}/get-comments`
                    )
                    .then(
                        (response) => {
                            self.comment_history = response.data.data;
                            if (response.data.success) {
                                Nova.success(response.data.message);
                            } else {
                                Nova.error(response.data.message);
                            }
                        },
                        (response) => {
                            Nova.error(response);
                        }
                    )
                    .finally(() => {
                        //
                    });
            },
        },
    };
</script>
