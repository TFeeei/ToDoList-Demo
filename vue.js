new Vue({
    el: "#root",
    data: {
        apiUrl: './DatabaseHandler.php',
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

        deletedTodoLine: "", // 削除されたTodo行を一時特定するために存在する変数
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
        sendDataToServer(url, data) {
            axios.post(url, data)
                .then(res => {
                    console.log(res.data);
                }).catch(err => {
                    console.log(err);
                }).then(() => {
                    this.getTodoList()
                })
        },
        // データを受け取る
        getTodoList() {
            axios.get(this.apiUrl + '?action=getData')
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
        // 追加もしくは編集をキャンセルする
        cancelClick() {
            this.isAddModelShow = false
            this.isEditModelShow = false
        },

        // Todoの削除
        deleteTodo(item, index) {
            this.currentClickIndex = parseInt(this.paginatedTodo[index].ID) // 現在操作しているtodo項目のIDを特定する
            this.deletedTodoLine = index

            setTimeout(() => {
                this.sendDataToServer(this.apiUrl + '?action=deleteData', {
                    id: this.currentClickIndex
                })
                this.deletedTodoLine = "";
            }, 800);
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