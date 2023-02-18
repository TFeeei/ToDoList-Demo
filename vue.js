new Vue({
    el: "#root",
    data: {
        toDoList: [],

        totalNum: "",
        addAlertShow: false,
        editAlertShow: false,

        inputTitle: "",
        inputContent: "",
        currentClickIndex: "",

        newTitle: "",
        newContent: "",

        inputSearch: ""
    },

    mounted() {
        this.getToDoList();
    },

    methods: {
        // データを獲得する
        getToDoList() {
            axios.get('./getData.php')
                .then((res) => {
                    this.toDoList = res.data;
                    // console.log(this.toDoList);
                })
                .catch(err => {
                    console.log(err);
                })
        },

        // Todoの新規作成
        addTodo() {
            this.addAlertShow = true
        },

        addSureClick() {
            if (this.newTitle && this.newContent) {
                let addData = {
                    title: this.newTitle,
                    content: this.newContent,
                }
                axios.post('./insertData.php', addData)
                    .then(res => {
                        console.log(res.data);
                    }).catch(err => {
                        console.log(err);
                    }).then(() => {
                        this.addAlertShow = false
                        this.getToDoList()
                    })
            } else {
                alert("タイトルと内容を両方入力してください")
            }
        },

        // Todoの編集
        editTodo(item, index) {
            this.editAlertShow = true
            this.inputTitle = item.title
            this.inputContent = item.content
            this.currentClickIndex = parseInt(this.toDoList[index].ID)
        },
        // 編集を確認する
        sureClick() {
            if (this.inputTitle && this.inputContent) {
                let updateData = {
                    title: this.inputTitle,
                    content: this.inputContent,
                    id: this.currentClickIndex
                }
                axios.post('./updateData.php', updateData)
                    .then(res => {
                        console.log(res.data);
                    }).catch(err => {
                        console.log(err);
                    }).then(() => {
                        this.editAlertShow = false
                        this.getToDoList()
                    })
            } else {
                alert("タイトルと内容を両方入力してください")
            }

        },
        // 編集をキャンセルする
        cancelClick() {
            this.addAlertShow = false
            this.editAlertShow = false
        },

        // Todoの削除
        deleteTodo(item, index) {
            // 把现在点击的todo项目的id传值
            this.currentClickIndex = parseInt(this.toDoList[index].ID)

            axios.post('./deleteData.php', {
                    id: this.currentClickIndex
                })
                .then(res => {
                    console.log(res.data);
                }).catch(err => {
                    console.log(err);
                }).then(() => {
                    this.getToDoList()
                })
        },

        // タイトルでTodoを検索する
        searchToDo() {
            this.toDoList = this.toDoList.filter((item) => {
                return item.title.includes(this.inputSearch)
            })
        }

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