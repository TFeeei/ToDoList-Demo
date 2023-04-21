new Vue({
    el: "#root",
    data: {
        apiUrl: './usePostTableHandle.php',
        todoList: [],

        isAddModelShow: false, // Todoを追加するモーダルを表示するフラグ
        inputNewTitle: "",
        inputNewContent: "",

        isEditModelShow: false, // Todoを編集するモーダルを表示するフラグ
        inputEditTitle: "",
        inputEditContent: "",

        inputSearch: "",

        currentClickIndex: "",
        currentPage: 1,
        todoPerPage: 5,

        isDeleteModelShow: false, //削除時の確認メッセージモーダルを表示するフラグ
        deletedTodoLine: "", // 削除されたTodo行を一時特定するために存在する変数
        animateIndex: "",

        selectedOption: "CreatedAtAsc",
    },

    mounted() {
        this.getTodoList();
    },

    computed: {
        // todoListデータを1ページに表示する件数によって分割する
        paginatedTodo() {
            const start = (this.currentPage - 1) * this.todoPerPage;
            const end = start + this.todoPerPage;
            return this.todoList.slice(start, end)
        },

        // 合計ページ数を計算する
        totalPages() {
            return Math.ceil(this.todoList.length / this.todoPerPage);
        },
    },

    methods: {
        // データを渡す
        sendDataToServer(url, data, deleteAnimate) {
            axios.post(url, data)
                .then(res => {
                    console.log(res.data);
                    deleteAnimate ? deleteAnimate() : this.getTodoList();
                }).catch(err => {
                    console.log(err);
                })
        },

        // データを受け取る
        getTodoList() {
            let apiUrl = this.apiUrl;
            if (this.selectedOption === "CreatedAtAsc") {
                apiUrl += '?action=getData'
            } else if (this.selectedOption === "UpDatedAtAsc") {
                apiUrl += '?action=sortBy' + this.selectedOption;
            }
            axios.get(apiUrl)
                .then((res) => {
                    this.todoList = res.data;
                })
                .catch(err => {
                    console.log(err);
                })
        },

        // Todoの新規作成
        addTodo() {
            this.isAddModelShow = true
        },
        addSureClick() {
            if (this.inputNewTitle && this.inputNewContent) {
                let addData = {
                    title: this.inputNewTitle,
                    content: this.inputNewContent,
                }
                this.sendDataToServer(this.apiUrl + '?action=insertData', addData)
                this.isAddModelShow = false
            } else {
                alert("タイトルと内容を両方入力してください")
            }

            // 最後のページを計算して移動する
            const finalPage = Math.ceil((this.todoList.length + 1) / this.todoPerPage)
            this.currentPage = finalPage
        },

        // Todoの編集
        editTodo(item, index) {
            this.isEditModelShow = true
            this.inputEditTitle = item.title
            this.inputEditContent = item.content
            this.currentClickIndex = parseInt(this.paginatedTodo[index].ID)
        },
        // 編集を確認する
        editSureClick() {
            if (this.inputEditTitle && this.inputEditContent) {
                let updateData = {
                    title: this.inputEditTitle,
                    content: this.inputEditContent,
                    id: this.currentClickIndex
                }
                this.sendDataToServer(this.apiUrl + '?action=updateData', updateData)
                this.isEditModelShow = false
            } else {
                alert("タイトルと内容を両方入力してください")
            }

        },

        // Todoの削除
        deleteTodo(item, index) {
            this.isDeleteModelShow = true
            this.currentClickIndex = parseInt(this.paginatedTodo[index].ID) // 現在操作しているtodo項目のIDを特定する
            this.animateIndex = index
        },
        // 削除を確認する
        deleteSureClick() {
            this.isDeleteModelShow = false

            let deleteAnimate = () => {
                this.deletedTodoLine = this.animateIndex;
                setTimeout(() => {
                    this.deletedTodoLine = ""
                    this.getTodoList()
                }, 700)
            }

            this.sendDataToServer(this.apiUrl + '?action=deleteData', {
                id: this.currentClickIndex
            }, deleteAnimate)
        },

        // 追加・編集・削除をキャンセルする
        cancelClick() {
            this.isAddModelShow = false
            this.isEditModelShow = false
            this.isDeleteModelShow = false
        },

        // タイトルでTodoを検索する
        searchToDo() {
            if (this.inputSearch) {
                this.currentPage = 1 // 検索したらcurrentPageも更新すること
                this.todoList = this.todoList.filter((item) => {
                    return item.title.includes(this.inputSearch)
                })
            } else {
                this.getTodoList()
            }
        },

        // 選択されたページに移動する
        setPage(pageNum) {
            this.currentPage = pageNum;
        },

    },

    filters: {
        // 日時データの処理
        dateFormate(value) {
            const date = new Date(value);
            const year = date.getFullYear();
            const month = date.getMonth() + 1;
            const day = date.getDate();
            return year + "年" + month + "月" + day + "日";
        }
    }

})