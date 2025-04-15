#ifndef PRODUCTWIDGET_H
#define PRODUCTWIDGET_H

#include <QDialog>

namespace Ui {
class productwidget;
}

class productwidget : public QDialog
{
    Q_OBJECT

public:
    explicit productwidget(QWidget *parent = nullptr);
    ~productwidget();

private:
    Ui::productwidget *ui;
};

#endif // PRODUCTWIDGET_H
