<template>
    <div
        v-if="comment_history && comment_history.length"
        class="tw-mb-8 tw-mt-2"
    >
        <h2
            v-if="field.activity_title"
            class="tw-text-sm tw-font-semibold tw-leading-6 dark:tw-text-gray-400 tw-text-gray-900 tw-mb-6"
        >
            {{ field.activity_title }}
        </h2>
        <div
            class="tw-flex tw-w-full tw-items-center tw-justify-center tw-rounded-md dark:tw-bg-none tw-bg-white tw-px-3 tw-py-2 tw-text-sm tw-font-semibold dark:tw-text-gray-400 tw-text-gray-900 tw-shadow-sm tw-border-b tw-mb-4 tw-cursor-pointer"
            v-if="field.limit && comment_history.length > field.limit"
            @click="show_all = !show_all"
        >
            <div v-if="!show_all">
                View all activity
                <span class="tw-text-xs tw-text-gray-400"
                    >({{ comment_history.length - field.limit }} more)</span
                >
            </div>
            <div v-if="show_all">Only show the latests</div>
        </div>
        <ul role="list" class="tw-space-y-6 js-history-list">
            <template v-for="(comment_history_item, index) in comment_history">
                <ActivityWithoutComment
                    v-bind:key="comment_history_item.id"
                    v-if="
                        !comment_history_item.comment ||
                        comment_history_item.is_hidden
                    "
                    :hidden="
                        field.limit &&
                        !show_all &&
                        index < comment_history.length - field.limit
                    "
                    :comment="comment_history_item"
                    :resourceName="resourceName"
                    :resourceId="resourceId"
                    :field="field"
                />

                <ActivityWithComment
                    v-bind:key="comment_history_item.id"
                    v-if="
                        comment_history_item.comment &&
                        !comment_history_item.is_hidden
                    "
                    :hidden="
                        field.limit &&
                        !show_all &&
                        index < comment_history.length - field.limit
                    "
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
    import ActivityWithComment from "./ActivityWithComment";
    import ActivityWithoutComment from "./ActivityWithoutComment";

    export default {
        props: ["resourceName", "resourceId", "field"],

        components: { ActivityWithComment, ActivityWithoutComment },

        data() {
            return {
                show_all: false,
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
                        `/nova-vendor/nova-activity/${this.resourceName}/${this.resourceId}/get-comments`
                    )
                    .then(
                        (response) => {
                            self.comment_history = response.data.data;
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
